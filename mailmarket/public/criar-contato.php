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
    $categoria = trim($_POST['categoria'] ?? '');
    $status = $_POST['status'] ?? 'ativo';

    if (empty($nome)) {
        $errors[] = 'O nome do contato é obrigatório.';
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Um e-mail válido é obrigatório.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO contatos (usuario_id, nome, email, categoria, status, criado_em) VALUES (?, ?, ?, ?, ?, NOW())');
        $stmt->execute([$usuario_id, $nome, $email, $categoria, $status]);
        $success = true;
    }
}
?>

<h1>Criar Contato</h1>

<?php if ($success): ?>
    <div class="alert alert-success">Contato criado com sucesso!</div>
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
        <input type="text" class="form-control" id="nome" name="nome" required value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>">
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">E-mail</label>
        <input type="email" class="form-control" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    </div>
    <div class="mb-3">
        <label for="categoria" class="form-label">Categoria</label>
        <input type="text" class="form-control" id="categoria" name="categoria" value="<?= htmlspecialchars($_POST['categoria'] ?? '') ?>">
    </div>
    <div class="mb-3">
        <label for="status" class="form-label">Status</label>
        <select class="form-select" id="status" name="status">
            <option value="ativo" <?= (($_POST['status'] ?? '') === 'ativo') ? 'selected' : '' ?>>Ativo</option>
            <option value="inativo" <?= (($_POST['status'] ?? '') === 'inativo') ? 'selected' : '' ?>>Inativo</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Criar Contato</button>
</form>

<?php
require_once __DIR__ . '/footer.php';
?>
