<?php
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/../config/config.php';

$usuario_id = $_SESSION['usuario_id'];
$pdo = getDBConnection();

$errors = [];
$preview = [];
$step = $_POST['step'] ?? 'upload';

if ($step === 'upload' && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Erro ao fazer upload do arquivo.';
    } else {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        if (strtolower($ext) !== 'csv') {
            $errors[] = 'Por favor, envie um arquivo CSV válido.';
        } else {
            $handle = fopen($file['tmp_name'], 'r');
            if ($handle === false) {
                $errors[] = 'Não foi possível abrir o arquivo CSV.';
            } else {
                $header = fgetcsv($handle);
                if (!$header || count($header) < 2) {
                    $errors[] = 'Arquivo CSV inválido ou com cabeçalho incorreto.';
                } else {
                    while (($row = fgetcsv($handle)) !== false) {
                        $preview[] = $row;
                    }
                }
                fclose($handle);
            }
        }
    }
} elseif ($step === 'import' && isset($_POST['contacts'])) {
    $contacts = $_POST['contacts'];
    $inserted = 0;
    foreach ($contacts as $contact) {
        $nome = trim($contact[0]);
        $email = trim($contact[1]);
        $categoria = trim($contact[2] ?? '');
        $status = in_array($contact[3] ?? 'ativo', ['ativo', 'inativo']) ? $contact[3] : 'ativo';

        if ($nome && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $stmt = $pdo->prepare('INSERT INTO contatos (usuario_id, nome, email, categoria, status, criado_em) VALUES (?, ?, ?, ?, ?, NOW())');
            $stmt->execute([$usuario_id, $nome, $email, $categoria, $status]);
            $inserted++;
        }
    }
    $success_message = "Importação concluída. $inserted contatos foram adicionados.";
    $step = 'done';
}
?>

<h1>Importar Contatos via CSV</h1>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if ($step === 'upload'): ?>
    <form method="post" enctype="multipart/form-data" novalidate>
        <input type="hidden" name="step" value="upload" />
        <div class="mb-3">
            <label for="csv_file" class="form-label">Arquivo CSV</label>
            <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv" required>
            <div class="form-text">O arquivo CSV deve conter colunas: nome, email, categoria (opcional), status (ativo/inativo, opcional).</div>
        </div>
        <button type="submit" class="btn btn-primary">Enviar e Visualizar</button>
    </form>
<?php elseif ($step === 'import'): ?>
    <form method="post" novalidate>
        <input type="hidden" name="step" value="import" />
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Categoria</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($preview as $index => $row): ?>
                    <tr>
                        <td><input type="text" name="contacts[<?= $index ?>][0]" value="<?= htmlspecialchars($row[0] ?? '') ?>" class="form-control" required></td>
                        <td><input type="email" name="contacts[<?= $index ?>][1]" value="<?= htmlspecialchars($row[1] ?? '') ?>" class="form-control" required></td>
                        <td><input type="text" name="contacts[<?= $index ?>][2]" value="<?= htmlspecialchars($row[2] ?? '') ?>" class="form-control"></td>
                        <td>
                            <select name="contacts[<?= $index ?>][3]" class="form-select">
                                <option value="ativo" <?= (isset($row[3]) && strtolower($row[3]) === 'ativo') ? 'selected' : '' ?>>Ativo</option>
                                <option value="inativo" <?= (isset($row[3]) && strtolower($row[3]) === 'inativo') ? 'selected' : '' ?>>Inativo</option>
                            </select>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit" class="btn btn-success">Importar Contatos</button>
        <a href="importar-contatos.php" class="btn btn-secondary">Cancelar</a>
    </form>
<?php elseif ($step === 'done'): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
    <a href="listar-contatos.php" class="btn btn-primary">Voltar para Lista de Contatos</a>
<?php endif; ?>

<?php
require_once __DIR__ . '/footer.php';
?>
