<?php
session_start();
require_once __DIR__ . '/../config/config.php';

if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit;
}

$erro = '';
$sucesso = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $senha = $_POST['senha'] ?? '';
    $senha_confirm = $_POST['senha_confirm'] ?? '';

    if (empty($nome)) {
        $erro = 'Por favor, insira seu nome.';
    } elseif (!$email) {
        $erro = 'Por favor, insira um e-mail válido.';
    } elseif (empty($senha) || strlen($senha) < 6) {
        $erro = 'A senha deve ter no mínimo 6 caracteres.';
    } elseif ($senha !== $senha_confirm) {
        $erro = 'As senhas não coincidem.';
    } else {
        $pdo = getDBConnection();
        // Check if email already exists
        $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $erro = 'Este e-mail já está cadastrado.';
        } else {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO usuarios (nome, email, senha_hash) VALUES (?, ?, ?)');
            if ($stmt->execute([$nome, $email, $senha_hash])) {
                $sucesso = 'Cadastro realizado com sucesso! Você pode fazer login agora.';
            } else {
                $erro = 'Erro ao cadastrar. Tente novamente.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cadastro - MailMarket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/style.css" />
    <script src="assets/js/validation.js" defer></script>
</head>
<body>
<div class="container">
    <h2 class="mb-4 text-center">Cadastro no MailMarket</h2>
    <div id="alert-container">
        <?php if ($erro): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($erro) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        <?php elseif ($sucesso): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($sucesso) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        <?php endif; ?>
    </div>
    <form id="registerForm" method="post" novalidate onsubmit="return validateRegisterForm()">
        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" class="form-control" id="nome" name="nome" required />
            <div class="invalid-feedback">Por favor, insira seu nome.</div>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" class="form-control" id="email" name="email" required />
            <div class="invalid-feedback">Por favor, insira um e-mail válido.</div>
        </div>
        <div class="mb-3">
            <label for="senha" class="form-label">Senha</label>
            <input type="password" class="form-control" id="senha" name="senha" required minlength="6" />
            <div class="invalid-feedback">A senha deve ter no mínimo 6 caracteres.</div>
        </div>
        <div class="mb-3">
            <label for="senha_confirm" class="form-label">Confirme a Senha</label>
            <input type="password" class="form-control" id="senha_confirm" name="senha_confirm" required minlength="6" />
            <div class="invalid-feedback">As senhas devem coincidir.</div>
        </div>
        <button type="submit" class="btn btn-primary w-100">Cadastrar</button>
    </form>
    <p class="mt-3 text-center">Já tem uma conta? <a href="login.php">Faça login</a></p>
</div>
<script>
function validateRegisterForm() {
    clearAlert();
    const nome = document.getElementById('nome').value.trim();
    const email = document.getElementById('email').value.trim();
    const senha = document.getElementById('senha').value.trim();
    const senha_confirm = document.getElementById('senha_confirm').value.trim();
    let valid = true;

    if (nome === '') {
        valid = false;
        document.getElementById('nome').classList.add('is-invalid');
    } else {
        document.getElementById('nome').classList.remove('is-invalid');
    }

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

    if (senha !== senha_confirm) {
        valid = false;
        document.getElementById('senha_confirm').classList.add('is-invalid');
    } else {
        document.getElementById('senha_confirm').classList.remove('is-invalid');
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
