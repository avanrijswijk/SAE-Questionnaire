<style>
    .kpi-box { border-top: 4px solid #ff0000; }
</style>

<div class="container mt-5 mb-6">
    
    <div class="level mb-5">
        <div class="level-left">
            <div>
                <a href="./?c=home" class="button is-small is-ghost mb-5">
                    <span>< Mes questionnaires</span>
                </a>
                <h1 class="title is-2 mb-5">
                    <?php echo htmlspecialchars($questionnaire['titre'] ?? 'Titre inconnu'); ?>
                </h1>
                <div class="tags">
                    <?php 
                    $est_expire = strtotime($questionnaire['date_expiration']) < time();
                    if ($est_expire): ?>
                        <span class="tag is-danger">Expiré</span>
                    <?php else: ?>
                        <span class="tag is-success">Actif (jusqu'au <?php echo date('d/m/Y', strtotime($questionnaire['date_expiration'])); ?>)</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="level-right">
            <div class="buttons">
                <a href="./?c=questionnaire&a=exporter&id=<?php echo $questionnaire['id']; ?>" class="button is-info">
                    <span>Exporter CSV</span>
                </a>
                <a href="####" class="button is-warning">
                    <span>Modifier</span>
                </a>
            </div>
        </div>
    </div>

    <div class="columns mb-5">
        <div class="column is-4">
            <div class="box kpi-box has-text-centered">
                <p class="heading is-size-6 has-text-grey">Total des réponses</p>
                <p class="title is-1"><?php echo $total_reponses;?></p>
            </div>
        </div>

        <div class="column is-4">
            <div class="box kpi-box is-success has-text-centered">
                <p class="heading is-size-6 has-text-grey">Dernière réponse le</p>
                <p class="title is-3 mt-3">
                    <?php 
                    if (!empty($repondants)) {
                        echo date('d/m/Y à H:i', strtotime($repondants[0]['date_reponse']));
                    } else {
                        echo "-";
                    }
                    ?>
                </p>
            </div>
        </div>

        <div class="column is-4">
            <div class="box kpi-box is-warning has-text-centered">
                <p class="heading is-size-6 has-text-grey">Nombre de questions</p>
                <p class="title is-1"><?php echo $total_questions ?? '?'; ?></p>
            </div>
        </div>
    </div>

    <div class="box">
        <div class="level">
            <div class="level-left">
                <h2 class="title is-4">Historique des participations</h2>
            </div>
            <div class="level-right">
                <div>
                    <input class="input is-small is-rounded" type="text" placeholder="Chercher un nom...">
                </div>
            </div>
        </div>

        <div class="table-container">
            <table class="table is-fullwidth is-striped is-hoverable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Date de réponse</th>
                        <th class="has-text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($repondants)): ?>
                        <tr>
                            <td colspan="5" class="has-text-centered py-5 has-text-grey">
                                <i>Aucune réponse pour le moment.</i>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($repondants as $index => $repondant): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars(strtoupper($repondant['nom'] ?? $repondant['id_utilisateur'] ?? 'Anonyme')); ?></td>
                                <td><?php echo htmlspecialchars(ucfirst($repondant['prenom'] ?? '')); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($repondant['date_reponse'])); ?></td>
                                <td class="has-text-right">
                                    <a href="####" class="button is-small is-link is-outlined">
                                        Voir ses réponses
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>