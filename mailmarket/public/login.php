<?php
session_start();
require_once __DIR__ . '/../config/config.php';

if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit;
}

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $senha = $_POST['senha'] ?? '';

    if (!$email) {
        $erro = 'Por favor, insira um e-mail válido.';
    } elseif (empty($senha)) {
        $erro = 'Por favor, insira a senha.';
    } else {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare('SELECT id, nome, senha_hash FROM usuarios WHERE email = ?');
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($senha, $usuario['senha_hash'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            header('Location: dashboard.php');
            exit;
        } else {
            $erro = 'E-mail ou senha incorretos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - MailMarket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/style.css" />
    <script src="assets/js/validation.js" defer></script>
</head>
<body>
<div class="container">
    <h2 class="mb-4 text-center">Login no MailMarket</h2>
    <div id="alert-container">
        <?php if ($erro): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($erro) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        <?php endif; ?>
    </div>
    <form id="loginForm" method="post" novalidate onsubmit="return validateLoginForm()">
        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" class="form-control" id="email" name="email" required />
            <div class="invalid-feedback">Por favor, insira um e-mail válido.</div>
        </div>
        <div class="mb-3">
            <label for="senha" class="form-label">Senha</label>
            <input type="password" class="form-control" id="senha" name="senha" required minlength="6" />
            <div class="invalid-feedback">Por favor, insira a senha (mínimo 6 caracteres).</div>
        </div>
        <button type="submit" class="btn btn-primary w-100">Entrar</button>
    </form>
    <p class="mt-3 text-center">Não tem uma conta? <a href="register.php">Cadastre-se</a></p>
</div>
<script>
function validateLoginForm() {
    clearAlert();
    const email = document.getElementById('email').value.trim();
    const senha = document.getElementById('senha').value.trim();
    let valid = true;

    if (!validateEmail(email)) {
        valid = false;
        document.getElementById('email').classList.add('is-invalid');
    } else {
        document.getElementById('email').classList.remove('is-invalid');
    }

    if (!validatePassword(senha)) {
        valid = false;
        document.getElementById('senha').classList.add('is-invalid');
    } else {
        document.getElementById('senha').classList.remove('is-invalid');
    }

    if (!valid) {
        showAlert('Por favor, corrija os erros no formulário.');
    }
    return valid;
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
