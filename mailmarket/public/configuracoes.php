<?php
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/../config/config.php';

$usuario_id = $_SESSION['usuario_id'];
$pdo = getDBConnection();

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha_atual = $_POST['senha_atual'] ?? '';
    $nova_senha = $_POST['nova_senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';

    if (empty($nome)) {
        $errors[] = 'O nome é obrigatório.';
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Um e-mail válido é obrigatório.';
    }

    if (!empty($nova_senha)) {
        if ($nova_senha !== $confirmar_senha) {
            $errors[] = 'A nova senha e a confirmação não coincidem.';
        } else {
            // Verify current password
            $stmt = $pdo->prepare('SELECT senha FROM usuarios WHERE id = ?');
            $stmt->execute([$usuario_id]);
            $usuario = $stmt->fetch();
            if (!$usuario || !password_verify($senha_atual, $usuario['senha'])) {
                $errors[] = 'Senha atual incorreta.';
            }
        }
    }

    if (empty($errors)) {
        $params = [$nome, $email, $usuario_id];
        $sql = 'UPDATE usuarios SET nome = ?, email = ?';

        if (!empty($nova_senha)) {
            $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
            $sql .= ', senha = ?';
            $params[] = $senha_hash;
        }

        $sql .= ' WHERE id = ?';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $_SESSION['usuario_nome'] = $nome;
        $success = true;
    }
} else {
    // Load current user data
    $stmt = $pdo->prepare('SELECT nome, email FROM usuarios WHERE id = ?');
    $stmt->execute([$usuario_id]);
    $usuario = $stmt->fetch();
    $nome = $usuario['nome'] ?? '';
    $email = $usuario['email'] ?? '';
}
?>

<h1>Configurações</h1>

<?php if ($success): ?>
    <div class="alert alert-success">Configurações atualizadas com sucesso!</div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" novalidate>
    <div class="mb-3">
        <label for="nome" class="form-label">Nome</label>
        <input type="text" class="form-control" id="nome" name="nome" required value="<?= htmlspecialchars($nome) ?>">
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">E-mail</label>
        <input type="email" class="form-control" id="email" name="email" required value="<?= htmlspecialchars($email) ?>">
    </div>
    <hr>
    <h5>Alterar Senha</h5>
    <div class="mb-3">
        <label for="senha_atual" class="form-label">Senha Atual</label>
        <input type="password" class="form-control" id="senha_atual" name="senha_atual">
    </div>
    <div class="mb-3">
        <label for="nova_senha" class="form-label">Nova Senha</label>
        <input type="password" class="form-control" id="nova_senha" name="nova_senha">
    </div>
    <div class="mb-3">
        <label for="confirmar_senha" class="form-label">Confirmar Nova Senha</label>
        <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha">
    </div>
    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
</form>

<?php
require_once __DIR__ . '/footer.php';
?>
