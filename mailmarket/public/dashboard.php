<?php
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/../config/config.php';

$usuario_nome = $_SESSION['usuario_nome'];
$usuario_id = $_SESSION['usuario_id'];

$pdo = getDBConnection();

// Fetch recent campaigns (limit 5)
$stmt = $pdo->prepare('SELECT id, nome, assunto, data_envio, status FROM campanhas WHERE usuario_id = ? ORDER BY criado_em DESC LIMIT 5');
$stmt->execute([$usuario_id]);
$campanhas = $stmt->fetchAll();

// Mock statistics data
$stats = [
    'campanhas_enviadas' => 12,
    'taxa_abertura' => 45,
    'cliques' => 120,
];
?>
<h1>Bem-vindo, <?= htmlspecialchars($usuario_nome) ?>!</h1>

<div class="row my-4">
    <div class="col-md-4">
        <div class="card text-white bg-success mb-3">
            <div class="card-header">Campanhas Enviadas</div>
            <div class="card-body">
                <h5 class="card-title"><?= $stats['campanhas_enviadas'] ?></h5>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-info mb-3">
            <div class="card-header">Taxa de Abertura (%)</div>
            <div class="card-body">
                <h5 class="card-title"><?= $stats['taxa_abertura'] ?>%</h5>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-primary mb-3">
            <div class="card-header">Cliques</div>
            <div class="card-body">
                <h5 class="card-title"><?= $stats['cliques'] ?></h5>
            </div>
        </div>
    </div>
</div>

<h3>Campanhas Recentes</h3>
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
            </tr>
        </thead>
        <tbody>
            <?php foreach ($campanhas as $campanha): ?>
                <tr>
                    <td><?= htmlspecialchars($campanha['nome']) ?></td>
                    <td><?= htmlspecialchars($campanha['assunto']) ?></td>
                    <td><?= $campanha['data_envio'] ? date('d/m/Y H:i', strtotime($campanha['data_envio'])) : '-' ?></td>
                    <td><?= ucfirst($campanha['status']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php
require_once __DIR__ . '/footer.php';
?>
