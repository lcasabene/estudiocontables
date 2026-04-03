<?php

namespace Controllers;

use Core\Database;
use Core\Session;

class BlogController
{
    // =============================================
    // PUBLIC METHODS (no auth required)
    // =============================================

    public function publicIndex(): void
    {
        $pdo = Database::master();

        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 6;
        $offset = ($page - 1) * $perPage;

        $total = (int)$pdo->query("SELECT COUNT(*) FROM blog_posts WHERE publicado = 1")->fetchColumn();
        $totalPages = max(1, ceil($total / $perPage));

        $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE publicado = 1 ORDER BY created_at DESC LIMIT :offset, :limit");
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->execute();
        $posts = $stmt->fetchAll();

        view('blog.public_index', [
            'posts' => $posts,
            'page' => $page,
            'totalPages' => $totalPages,
        ]);
    }

    public function publicShow(string $slug): void
    {
        $pdo = Database::master();
        $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE slug = :slug AND publicado = 1");
        $stmt->execute(['slug' => $slug]);
        $post = $stmt->fetch();

        if (!$post) {
            http_response_code(404);
            view('errors.404');
            return;
        }

        $commentsStmt = $pdo->prepare("SELECT * FROM blog_comments WHERE post_id = :id AND aprobado = 1 ORDER BY created_at ASC");
        $commentsStmt->execute(['id' => $post['id']]);
        $comments = $commentsStmt->fetchAll();

        $commentCount = count($comments);

        $votesStmt = $pdo->prepare("SELECT vote, COUNT(*) as total FROM blog_post_votes WHERE post_id = :id GROUP BY vote");
        $votesStmt->execute(['id' => $post['id']]);
        $votesRaw = $votesStmt->fetchAll();
        $likes = 0; $dislikes = 0;
        foreach ($votesRaw as $v) {
            if ($v['vote'] === 'like') $likes = (int)$v['total'];
            if ($v['vote'] === 'dislike') $dislikes = (int)$v['total'];
        }

        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $ip = trim(explode(',', $ip)[0]);
        $myVoteStmt = $pdo->prepare("SELECT vote FROM blog_post_votes WHERE post_id = :id AND ip_address = :ip");
        $myVoteStmt->execute(['id' => $post['id'], 'ip' => $ip]);
        $myVote = $myVoteStmt->fetchColumn() ?: null;

        view('blog.public_show', [
            'post' => $post,
            'comments' => $comments,
            'commentCount' => $commentCount,
            'likes' => $likes,
            'dislikes' => $dislikes,
            'myVote' => $myVote,
        ]);
    }

    public function vote(string $slug): void
    {
        header('Content-Type: application/json');
        $pdo = Database::master();

        $stmt = $pdo->prepare("SELECT id FROM blog_posts WHERE slug = :slug AND publicado = 1");
        $stmt->execute(['slug' => $slug]);
        $post = $stmt->fetch();

        if (!$post) {
            echo json_encode(['error' => 'Post not found']);
            return;
        }

        $vote = $_POST['vote'] ?? '';
        if (!in_array($vote, ['like', 'dislike'])) {
            echo json_encode(['error' => 'Invalid vote']);
            return;
        }

        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $ip = trim(explode(',', $ip)[0]);

        $existing = $pdo->prepare("SELECT vote FROM blog_post_votes WHERE post_id = :id AND ip_address = :ip");
        $existing->execute(['id' => $post['id'], 'ip' => $ip]);
        $current = $existing->fetchColumn();

        if ($current === $vote) {
            $pdo->prepare("DELETE FROM blog_post_votes WHERE post_id = :id AND ip_address = :ip")
                ->execute(['id' => $post['id'], 'ip' => $ip]);
        } else {
            $pdo->prepare("INSERT INTO blog_post_votes (post_id, ip_address, vote) VALUES (:id, :ip, :vote)
                           ON DUPLICATE KEY UPDATE vote = :vote2")
                ->execute(['id' => $post['id'], 'ip' => $ip, 'vote' => $vote, 'vote2' => $vote]);
        }

        $counts = $pdo->prepare("SELECT vote, COUNT(*) as total FROM blog_post_votes WHERE post_id = :id GROUP BY vote");
        $counts->execute(['id' => $post['id']]);
        $likes = 0; $dislikes = 0;
        foreach ($counts->fetchAll() as $v) {
            if ($v['vote'] === 'like') $likes = (int)$v['total'];
            if ($v['vote'] === 'dislike') $dislikes = (int)$v['total'];
        }

        $myVoteStmt = $pdo->prepare("SELECT vote FROM blog_post_votes WHERE post_id = :id AND ip_address = :ip");
        $myVoteStmt->execute(['id' => $post['id'], 'ip' => $ip]);
        $myVote = $myVoteStmt->fetchColumn() ?: null;

        echo json_encode(['likes' => $likes, 'dislikes' => $dislikes, 'myVote' => $myVote]);
    }

    public function storeComment(string $slug): void
    {
        $pdo = Database::master();
        $stmt = $pdo->prepare("SELECT id FROM blog_posts WHERE slug = :slug AND publicado = 1");
        $stmt->execute(['slug' => $slug]);
        $post = $stmt->fetch();

        if (!$post) {
            http_response_code(404);
            view('errors.404');
            return;
        }

        $nombre = trim($_POST['nombre'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $comentario = trim($_POST['comentario'] ?? '');

        if ($nombre === '' || $comentario === '') {
            Session::flash('blog_error', 'El nombre y comentario son obligatorios.');
            redirect(base_url("blog/{$slug}#comentarios"));
            return;
        }

        $insert = $pdo->prepare("INSERT INTO blog_comments (post_id, nombre, email, comentario) VALUES (:post_id, :nombre, :email, :comentario)");
        $insert->execute([
            'post_id' => $post['id'],
            'nombre' => $nombre,
            'email' => $email ?: null,
            'comentario' => $comentario,
        ]);

        Session::flash('blog_success', 'Comentario publicado exitosamente.');
        redirect(base_url("blog/{$slug}#comentarios"));
    }

    // =============================================
    // ADMIN METHODS (auth required)
    // =============================================

    public function adminIndex(): void
    {
        $pdo = Database::master();
        $posts = $pdo->query("SELECT bp.*, (SELECT COUNT(*) FROM blog_comments bc WHERE bc.post_id = bp.id) as comment_count, (SELECT COUNT(*) FROM blog_post_votes bv WHERE bv.post_id = bp.id AND bv.vote = 'like') as likes, (SELECT COUNT(*) FROM blog_post_votes bv2 WHERE bv2.post_id = bp.id AND bv2.vote = 'dislike') as dislikes FROM blog_posts bp ORDER BY bp.created_at DESC")->fetchAll();
        view('blog.admin_index', ['posts' => $posts]);
    }

    public function create(): void
    {
        view('blog.admin_form', ['post' => null]);
    }

    public function store(): void
    {
        $pdo = Database::master();
        $data = $this->validateInput();

        $stmt = $pdo->prepare("INSERT INTO blog_posts (titulo, slug, resumen, contenido, imagen_url, publicado) 
                               VALUES (:titulo, :slug, :resumen, :contenido, :imagen_url, :publicado)");
        try {
            $stmt->execute($data);
            Session::flash('success', 'Articulo creado exitosamente.');
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                Session::flash('error', 'Ya existe un articulo con ese slug.');
            } else {
                Session::flash('error', 'Error al crear el articulo.');
            }
            redirect(tenant_url('blog/crear'));
            return;
        }
        redirect(tenant_url('blog'));
    }

    public function edit(int $id): void
    {
        $pdo = Database::master();
        $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $post = $stmt->fetch();

        if (!$post) {
            http_response_code(404);
            view('errors.404');
            return;
        }

        view('blog.admin_form', ['post' => $post]);
    }

    public function update(int $id): void
    {
        $pdo = Database::master();
        $data = $this->validateInput();
        $data['id'] = $id;

        $stmt = $pdo->prepare("UPDATE blog_posts SET titulo = :titulo, slug = :slug, resumen = :resumen, 
                               contenido = :contenido, imagen_url = :imagen_url, publicado = :publicado,
                               updated_at = NOW() WHERE id = :id");
        try {
            $stmt->execute($data);
            Session::flash('success', 'Articulo actualizado exitosamente.');
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                Session::flash('error', 'Ya existe un articulo con ese slug.');
            } else {
                Session::flash('error', 'Error al actualizar el articulo.');
            }
        }
        redirect(tenant_url("blog/{$id}/editar"));
    }

    public function delete(int $id): void
    {
        $pdo = Database::master();
        $stmt = $pdo->prepare("DELETE FROM blog_posts WHERE id = :id");
        $stmt->execute(['id' => $id]);
        Session::flash('success', 'Articulo eliminado exitosamente.');
        redirect(tenant_url('blog'));
    }

    private function validateInput(): array
    {
        $titulo = trim($_POST['titulo'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        if ($slug === '') {
            $slug = $this->generateSlug($titulo);
        }

        return [
            'titulo' => $titulo,
            'slug' => $slug,
            'resumen' => trim($_POST['resumen'] ?? '') ?: null,
            'contenido' => $_POST['contenido'] ?? '',
            'imagen_url' => trim($_POST['imagen_url'] ?? '') ?: null,
            'publicado' => isset($_POST['publicado']) ? 1 : 0,
        ];
    }

    private function generateSlug(string $text): string
    {
        $slug = mb_strtolower($text);
        $slug = preg_replace('/[áàäâ]/u', 'a', $slug);
        $slug = preg_replace('/[éèëê]/u', 'e', $slug);
        $slug = preg_replace('/[íìïî]/u', 'i', $slug);
        $slug = preg_replace('/[óòöô]/u', 'o', $slug);
        $slug = preg_replace('/[úùüû]/u', 'u', $slug);
        $slug = preg_replace('/ñ/u', 'n', $slug);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        return trim($slug, '-');
    }
}
