<?php
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/../config/config.php';

$usuario_id = $_SESSION['usuario_id'];

$pdo = getDBConnection();

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $assunto = trim($_POST['assunto'] ?? '');
    $conteudo = trim($_POST['conteudo'] ?? '');
    $agendamento = trim($_POST['agendamento'] ?? '');

    if (empty($nome)) {
        $errors[] = 'O nome da campanha é obrigatório.';
    }
    if (empty($assunto)) {
        $errors[] = 'O assunto da campanha é obrigatório.';
    }
    if (empty($conteudo)) {
        $errors[] = 'O conteúdo da campanha é obrigatório.';
    }

    // Handle image upload
    $imagem_path = null;
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $tmp_name = $_FILES['imagem']['tmp_name'];
        $filename = basename($_FILES['imagem']['name']);
        $target_file = $upload_dir . $filename;
        if (move_uploaded_file($tmp_name, $target_file)) {
            $imagem_path = 'uploads/' . $filename;
        } else {
            $errors[] = 'Erro ao fazer upload da imagem.';
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO campanhas (usuario_id, nome, assunto, conteudo, imagem, agendamento, status, criado_em) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())');
        $status = 'pendente';
        $stmt->execute([$usuario_id, $nome, $assunto, $conteudo, $imagem_path, $agendamento, $status]);
        $success = true;
    }
}
?>

<h1>Criar Campanha</h1>

<?php if ($success): ?>
    <div class="alert alert-success">Campanha criada com sucesso!</div>
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

<form method="post" enctype="multipart/form-data" novalidate>
    <div class="mb-3">
        <label for="nome" class="form-label">Nome da Campanha</label>
        <input type="text" class="form-control" id="nome" name="nome" required value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>">
    </div>
    <div class="mb-3">
        <label for="assunto" class="form-label">Assunto</label>
        <input type="text" class="form-control" id="assunto" name="assunto" required value="<?= htmlspecialchars($_POST['assunto'] ?? '') ?>">
    </div>
    <div class="mb-3">
        <label for="conteudo" class="form-label">Conteúdo da Campanha</label>
        <textarea class="form-control" id="conteudo" name="conteudo" rows="6" required><?= htmlspecialchars($_POST['conteudo'] ?? '') ?></textarea>
    </div>
    <div class="mb-3">
        <label for="imagem" class="form-label">Upload de Imagem</label>
        <input type="file" class="form-control" id="imagem" name="imagem" accept="image/*">
    </div>
    <div class="mb-3">
        <label for="agendamento" class="form-label">Agendamento</label>
        <input type="datetime-local" class="form-control" id="agendamento" name="agendamento" value="<?= htmlspecialchars($_POST['agendamento'] ?? '') ?>">
    </div>
    <button type="submit" class="btn btn-primary">Criar Campanha</button>
</form>

<?php
require_once __DIR__ . '/footer.php';
?>
