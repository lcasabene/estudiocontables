<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EstudioContable SaaS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .hero-card {
            background: #fff;
            border-radius: 1rem;
            padding: 3rem;
            max-width: 500px;
            width: 100%;
            text-align: center;
            box-shadow: 0 25px 50px rgba(0,0,0,.25);
        }
        .hero-icon { font-size: 3rem; color: #2563eb; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class="hero-card">
        <div class="hero-icon"><i class="bi bi-building"></i></div>
        <h1 class="h3 fw-bold mb-2">EstudioContable SaaS</h1>
        <p class="text-muted mb-4">Sistema de Gestión para Estudios Contables</p>
        <div class="mb-3">
            <p class="small text-muted">Ingrese a su estudio mediante la URL:</p>
            <code class="fs-6"><?= base_url('{slug-del-estudio}/login') ?></code>
        </div>
        <hr>
        <p class="small text-muted mb-2">Estudio de demostración:</p>
        <a href="<?= base_url('demo/login') ?>" class="btn btn-primary btn-lg px-4">
            <i class="bi bi-box-arrow-in-right"></i> Ingresar al Demo
        </a>
        <p class="mt-3 small text-muted">Usuario: admin@estudio.com | Clave: admin123</p>
    </div>
</body>
</html>
