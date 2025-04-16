<?php
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/../config/config.php';

$usuario_id = $_SESSION['usuario_id'];
$pdo = getDBConnection();

$search = trim($_GET['search'] ?? '');
$categoria_filter = $_GET['categoria'] ?? '';
$status_filter = $_GET['status'] ?? '';

$query = 'SELECT id, nome, email, categoria, status FROM contatos WHERE usuario_id = ?';
$params = [$usuario_id];

if ($search !== '') {
    $query .= ' AND nome LIKE ?';
    $params[] = '%' . $search . '%';
}
if ($categoria_filter !== '') {
    $query .= ' AND categoria = ?';
    $params[] = $categoria_filter;
}
if ($status_filter !== '') {
    $query .= ' AND status = ?';
    $params[] = $status_filter;
}

$query .= ' ORDER BY nome ASC';

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$contatos = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['excluir_id'])) {
        $excluir_id = (int)$_POST['excluir_id'];
        $stmt = $pdo->prepare('DELETE FROM contatos WHERE id = ? AND usuario_id = ?');
        $stmt->execute([$excluir_id, $usuario_id]);
        header('Location: listar-contatos.php');
        exit;
    }
}

// Fetch distinct categories for filter dropdown
$catStmt = $pdo->prepare('SELECT DISTINCT categoria FROM contatos WHERE usuario_id = ? AND categoria IS NOT NULL AND categoria != ""');
$catStmt->execute([$usuario_id]);
$categorias = $catStmt->fetchAll(PDO::FETCH_COLUMN);
?>

<h1>Listar Contatos</h1>

<form method="get" class="row g-3 mb-3">
    <div class="col-md-4">
        <input type="text" name="search" class="form-control" placeholder="Buscar por nome" value="<?= htmlspecialchars($search) ?>">
    </div>
    <div class="col-md-3">
        <select name="categoria" class="form-select">
            <option value="">Todas as Categorias</option>
            <?php foreach ($categorias as $cat): ?>
                <option value="<?= htmlspecialchars($cat) ?>" <?= ($categoria_filter === $cat) ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3">
        <select name="status" class="form-select">
            <option value="">Todos os Status</option>
            <option value="ativo" <?= ($status_filter === 'ativo') ? 'selected' : '' ?>>Ativo</option>
            <option value="inativo" <?= ($status_filter === 'inativo') ? 'selected' : '' ?>>Inativo</option>
        </select>
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-primary w-100">Filtrar</button>
    </div>
</form>

<a href="criar-contato.php" class="btn btn-success mb-3">Adicionar Contato</a>

<?php if (count($contatos) === 0): ?>
    <p>Nenhum contato encontrado.</p>
<?php else: ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Categoria</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($contatos as $contato): ?>
                <tr>
                    <td><?= htmlspecialchars($contato['nome']) ?></td>
                    <td><?= htmlspecialchars($contato['email']) ?></td>
                    <td><?= htmlspecialchars($contato['categoria']) ?></td>
                    <td><?= ucfirst($contato['status']) ?></td>
                    <td>
                        <a href="editar-contato.php?id=<?= $contato['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
                        <form method="post" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir este contato?');">
                            <input type="hidden" name="excluir_id" value="<?= $contato['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php
require_once __DIR__ . '/footer.php';
?>
