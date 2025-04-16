<?php
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/../config/config.php';

$usuario_id = $_SESSION['usuario_id'];
$pdo = getDBConnection();

$stmt = $pdo->prepare('SELECT id, nome, email, categoria, tags, status FROM contatos WHERE usuario_id = ? ORDER BY nome ASC');
$stmt->execute([$usuario_id]);
$contatos = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['excluir_id'])) {
        $excluir_id = (int)$_POST['excluir_id'];
        $stmt = $pdo->prepare('DELETE FROM contatos WHERE id = ? AND usuario_id = ?');
        $stmt->execute([$excluir_id, $usuario_id]);
        header('Location: contatos.php');
        exit;
    }
}
?>

<h1>Contatos</h1>

<a href="criar-contato.php" class="btn btn-success mb-3">Adicionar Contato</a>

<?php if (count($contatos) === 0): ?>
    <p>Você ainda não adicionou nenhum contato.</p>
<?php else: ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Categoria</th>
                <th>Tags</th>
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
                    <td><?= htmlspecialchars($contato['tags']) ?></td>
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
