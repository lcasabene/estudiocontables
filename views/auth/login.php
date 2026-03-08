<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - <?= e(Core\Tenant::name() ?? 'Estudio') ?></title>
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
        .login-card {
            background: #fff;
            border-radius: 1rem;
            padding: 2.5rem;
            max-width: 420px;
            width: 100%;
            box-shadow: 0 25px 50px rgba(0,0,0,.25);
        }
        .login-brand {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-brand i { font-size: 2.5rem; color: #2563eb; }
        .login-brand h2 { font-weight: 700; margin-top: .5rem; color: #1e293b; }
        .login-brand p { color: #64748b; font-size: .9rem; }
        .form-control:focus { border-color: #2563eb; box-shadow: 0 0 0 .2rem rgba(37,99,235,.15); }
        .btn-primary { background: #2563eb; border-color: #2563eb; }
        .btn-primary:hover { background: #1d4ed8; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-brand">
            <i class="bi bi-building"></i>
            <h2><?= e(Core\Tenant::name() ?? 'Estudio') ?></h2>
            <p>Ingrese sus credenciales para acceder</p>
        </div>

        <?php if ($error = get_flash('error')): ?>
            <div class="alert alert-danger py-2">
                <i class="bi bi-exclamation-triangle"></i> <?= e($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= base_url($slug . '/login') ?>">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?= old('email') ?>" required autofocus placeholder="correo@ejemplo.com">
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label fw-semibold">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" 
                           required placeholder="Ingrese su contraseña">
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                <i class="bi bi-box-arrow-in-right"></i> Ingresar
            </button>
        </form>
    </div>
</body>
</html>
