<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/wordcloud2.js/1.2.2/wordcloud2.min.js"></script>


<div class="container mt-5 mb-6">
    
    <div class="level mb-5">
        <div class="level-left">
            <h1 class="title is-2">Analyse des résultats</h1>
        </div>
        <div class="level-right">
            <a href ='./?c=questionnaire&a=detail&id=<?php echo $questionnaire['id']; ?>' class="button is-link">Retour au tableau de bord</a>
        </div>
    </div>

   <h2 class="title is-4 mt-6">Questions à choix (Graphiques)</h2>
    <div class="columns is-multiline">
        <div id="conteneur-graphiques" class="contents" style="display: contents;"></div>
    </div>

    <?php if (!empty($questions_ouvertes)): ?>
        <h2 class="title is-4 mt-6">Réponses libres (Nuages de mots)</h2>
        
        <div id="conteneur-nuages" class="columns is-multiline mt-4 mb-6"></div>
    <?php endif; ?>
</div>


<script>

document.addEventListener('DOMContentLoaded', function() {
    
    const donneesStats = <?php echo $json_statistiques ?: '[]'; ?>;
    const conteneur = document.getElementById('conteneur-graphiques');

    if (donneesStats.length === 0) {
        conteneur.innerHTML = '<div class="column is-12"><div class="notification is-info is-light">Aucune donnée de type "choix multiple" à analyser pour ce questionnaire.</div></div>';
        return;
    }

    donneesStats.forEach(function(stat) {
        const divColumn = document.createElement('div');
        divColumn.className = 'column is-6';
        
        divColumn.innerHTML = `
            <div class="box" style="height: 100%;">
                <h3 class="title is-5 has-text-centered mb-4">${stat.titre_question}</h3>
                <div style="position: relative; height: 300px; width: 100%;">
                    <canvas id="graphique-${stat.id_question}"></canvas>
                </div>
            </div>
        `;
        conteneur.appendChild(divColumn);

        const canvasId = 'graphique-' + stat.id_question;
        const ctx = document.getElementById(canvasId);
        
        if (ctx) {
            new Chart(ctx.getContext('2d'), {
                type: stat.type_graphique,
                data: {
                    labels: stat.labels,
                    datasets: [{
                        label: 'Votes',
                        data: stat.donnees,
                        backgroundColor: stat.couleurs,
                        borderWidth: 1,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                    scales: stat.type_graphique === 'bar' ? { y: { beginAtZero: true, ticks: { stepSize: 1 } } } : {}
                }
            });
        } else {
            console.error("Impossible de trouver le canvas : ", canvasId);
        }
    });


    const questionsTextes = <?php echo !empty($questions_ouvertes) ? json_encode(array_values($questions_ouvertes)) : '[]'; ?>;

    if (questionsTextes.length > 0) {
        
        const conteneurTexte = document.createElement('div');
        conteneurTexte.className = 'columns is-multiline mt-4 mb-6';
        document.querySelector('.container').appendChild(conteneurTexte);

        const motsExclus = ['le', 'la', 'les', 'un', 'une', 'des', 'est', 'sont', 'pour', 'dans', 'sur', 'avec', 'que', 'qui', 'quoi', 'dont', 'mais', 'ou', 'et', 'donc', 'or', 'ni', 'car', 'pas', 'plus', 'très', 'trop', 'bien', 'tout', 'tous', 'cette', 'ceux', 'aux'];

        questionsTextes.forEach(function(q, index) {
            
            if (!q.reponses || q.reponses.length === 0) return;

            let toutLeTexte = q.reponses.join(" ").toLowerCase();
            let mots = toutLeTexte.replace(/[.,!?;"'()]/g, " ").split(/\s+/);
            
            let compteMots = {};
            mots.forEach(function(mot) {
                if (mot.length > 3 && !motsExclus.includes(mot)) {
                    compteMots[mot] = (compteMots[mot] || 0) + 1;
                }
            });

            let motsTries = Object.keys(compteMots)
                .map(mot => [mot, compteMots[mot]])
                .sort((a, b) => b[1] - a[1])
                .slice(0, 30); 

            if (motsTries.length > 0) {
                const divColumn = document.createElement('div');
                divColumn.className = 'column is-6';
                const canvasId = 'nuage-mots-' + index;
                
                divColumn.innerHTML = `
                    <div class="box" style="height: 100%;">
                        <h3 class="title is-6 has-text-centered has-text-info mb-4">
                            Nuage de mots :<br> <small>${q.titre}</small>
                        </h3>
                        <div style="display: flex; justify-content: center; align-items: center;">
                            <canvas id="${canvasId}" width="450" height="250"></canvas>
                        </div>
                    </div>
                `;
                conteneurTexte.appendChild(divColumn);

                WordCloud(document.getElementById(canvasId), {
                    list: motsTries,
                    weightFactor: function (size) {
                        return size * 15; 
                    },
                    fontFamily: 'sans-serif',
                    color: 'random-dark',
                    rotateRatio: 0.3,
                    rotationSteps: 2,
                    backgroundColor: '#ffffff'
                });
            }
        });
    }
});

</script>