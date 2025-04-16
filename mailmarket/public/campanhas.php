<?php
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/../config/config.php';

$usuario_id = $_SESSION['usuario_id'];
$pdo = getDBConnection();

$stmt = $pdo->prepare('SELECT id, nome, assunto, data_envio, status FROM campanhas WHERE usuario_id = ? ORDER BY criado_em DESC');
$stmt->execute([$usuario_id]);
$campanhas = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['excluir_id'])) {
        $excluir_id = (int)$_POST['excluir_id'];
        $stmt = $pdo->prepare('DELETE FROM campanhas WHERE id = ? AND usuario_id = ?');
        $stmt->execute([$excluir_id, $usuario_id]);
        header('Location: campanhas.php');
        exit;
    }
}
?>

<h1>Campanhas</h1>

<a href="criar-campanha.php" class="btn btn-success mb-3">Criar Nova Campanha</a>

<?php if (count($campanhas) === 0): ?>
    <p>Você ainda não criou nenhuma campanha.</p>
<?php else: ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Assunto</th>
                <th>Data de Envio</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($campanhas as $campanha): ?>
                <tr>
                    <td><?= htmlspecialchars($campanha['nome']) ?></td>
                    <td><?= htmlspecialchars($campanha['assunto']) ?></td>
                    <td><?= $campanha['data_envio'] ? date('d/m/Y H:i', strtotime($campanha['data_envio'])) : '-' ?></td>
                    <td><?= ucfirst($campanha['status']) ?></td>
                    <td>
                        <a href="editar-campanha.php?id=<?= $campanha['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
                        <a href="duplicar-campanha.php?id=<?= $campanha['id'] ?>" class="btn btn-sm btn-secondary">Duplicar</a>
                        <form method="post" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir esta campanha?');">
                            <input type="hidden" name="excluir_id" value="<?= $campanha['id'] ?>">
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
