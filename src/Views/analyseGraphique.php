<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container mt-5 mb-6">
    
    <div class="level mb-5">
        <div class="level-left">
            <h1 class="title is-2">Analyse des résultats</h1>
        </div>
        <div class="level-right">
            <a href ='./?c=questionnaire&a=detail&id=<?php echo $questionnaire['id']; ?>' class="button is-link">Retour au tableau de bord</a>
        </div>
    </div>

    <div class="columns is-multiline">

        <?php foreach ($statistiques as $stat): ?>
        
            <div class="column is-6">
                <div class="box" style="height: 100%;">
                    <h3 class="title is-5 has-text-centered mb-4">
                        <?php echo htmlspecialchars($stat['titre_question']); ?>
                    </h3>
                    
                    <div style="position: relative; height: 300px; width: 100%;">
                        <canvas id="graphique-<?php echo $stat['id_question']; ?>"></canvas>
                    </div>
                </div>
            </div>

        <?php endforeach; ?>

    </div>
</div>


<script>
    // 1. On récupère les données PHP proprement en JavaScript
    // Le 'echo' va écrire le JSON directement dans le code JS
    const donneesStats = <?php echo $json_statistiques; ?>;

    // 2. On parcourt chaque question pour créer son graphique
    donneesStats.forEach(function(stat) {
        
        // On cible le bon <canvas>
        const ctx = document.getElementById('graphique-' + stat.id_question).getContext('2d');

        // On crée le graphique avec Chart.js
        new Chart(ctx, {
            type: stat.type_graphique, // 'pie', 'bar', 'doughnut'...
            data: {
                labels: stat.labels, // Les choix possibles
                datasets: [{
                    label: 'Nombre de réponses',
                    data: stat.donnees, // Les votes
                    backgroundColor: stat.couleurs,
                    borderWidth: 1,
                    borderColor: '#ffffff' // Bordure blanche pour faire propre
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // Permet au graphique de bien remplir sa boîte Bulma
                plugins: {
                    legend: {
                        position: 'bottom' // Place la légende en dessous du graphique
                    }
                },
                // Si c'est un graphique en barres, on force l'axe Y à commencer à 0
                scales: stat.type_graphique === 'bar' ? {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                } : {}
            }
        });
    });
</script>