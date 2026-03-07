<?php
$title = 'Statistiques';
$pageCss = 'statistiques.css';
ob_start();
?>

<div class="btn-container">
    <h2 class="main-btn">STATISTIQUES</h2>
</div>

<div class="container">

    <!-- TABLEAU -->
    <div class="table-section">
        <table>
            <caption class="table-caption">TABLEAU DES COMMANDES PAR MENU</caption>
            <thead>
                <tr>
                    <th scope="col">Menu</th>
                    <th scope="col">Nb commandes</th>
                    <th scope="col">CA (€)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($statsParMenu)): ?>
                    <tr><td colspan="3" class="text-center">Aucune donnée.</td></tr>
                <?php else: ?>
                    <?php foreach ($statsParMenu as $stat): ?>
                        <tr>
                            <th scope="row"><?= htmlspecialchars($stat['menu_titre']) ?></th>
                            <td class="nbCommande"><?= (int)$stat['nb_commandes'] ?></td>
                            <td class="total"><?= number_format((float)$stat['ca_total'], 2, ',', ' ') ?> €</td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="total">
                        <th scope="row">TOTAL</th>
                        <td><?= array_sum(array_column($statsParMenu, 'nb_commandes')) ?></td>
                        <td><?= number_format(array_sum(array_column($statsParMenu, 'ca_total')), 2, ',', ' ') ?> €</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- GRAPHIQUE -->
    <div class="chart-section">
        <div class="chart-title">GRAPHIQUE DES COMMANDES</div>
        <div style="height: 400px;">
            <canvas id="graphiqueStats"
                    role="img"
                    aria-label="Graphique barres — commandes par menu"></canvas>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    const labels = <?= json_encode(array_column($statsParMenu, 'menu_titre')) ?>;
    const nbCmds = <?= json_encode(array_column($statsParMenu, 'nb_commandes')) ?>;

    new Chart(document.getElementById('graphiqueStats').getContext('2d'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Nb commandes',
                data: nbCmds,
                backgroundColor: '#6ec585',
                borderRadius: 6,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
})();
</script>

<?php
$content = ob_get_clean();
require_once ROOT_PATH . '/templates/employee/layout/base.php';
?>