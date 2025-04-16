<?php
require_once __DIR__ . '/header.php';
?>

<h1>Estatísticas</h1>

<canvas id="graficoAbertura" width="400" height="200"></canvas>
<canvas id="graficoCliques" width="400" height="200" class="mt-4"></canvas>

<script>
const ctxAbertura = document.getElementById('graficoAbertura').getContext('2d');
const graficoAbertura = new Chart(ctxAbertura, {
    type: 'bar',
    data: {
        labels: ['Campanha 1', 'Campanha 2', 'Campanha 3', 'Campanha 4', 'Campanha 5'],
        datasets: [{
            label: 'Taxa de Abertura (%)',
            data: [45, 60, 30, 50, 70],
            backgroundColor: 'rgba(54, 162, 235, 0.7)'
        }]
    },
    options: {
        scales: {
            y: { beginAtZero: true, max: 100 }
        }
    }
});

const ctxCliques = document.getElementById('graficoCliques').getContext('2d');
const graficoCliques = new Chart(ctxCliques, {
    type: 'line',
    data: {
        labels: ['Campanha 1', 'Campanha 2', 'Campanha 3', 'Campanha 4', 'Campanha 5'],
        datasets: [{
            label: 'Cliques',
            data: [120, 150, 80, 100, 200],
            borderColor: 'rgba(255, 99, 132, 0.7)',
            fill: false,
            tension: 0.1
        }]
    },
    options: {
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>

<?php
require_once __DIR__ . '/footer.php';
?>
