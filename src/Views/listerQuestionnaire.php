<main style="background-color: #EFEFEF; height: 100%; overflow-y: auto;">
    <div>
        <script src="./src/Views/js/lister_questionnaire/modals/code/modalCode.js"></script>
        <button style="background-color: #F5A320;border-radius: 0 0 100px 0;" id="bouton-code">
            <h3 class="title is-4 p-2 pr-6 is-3 has-text-weight-semibold">J'ai un code</h3>
        </button>
        <div id="dialog-code" class="modal">
            <div class="modal-background"></div>
            <form id="form-enregistrer" class="modal-card">
                <section class="modal-card-body" style="border-radius: 15px;">
                    <button id="bouton-fermer" class="delete" aria-label="close" style="position: absolute; right:10px; top: 10px;"></button>
                    <div class="field">
                        <label class="label">Code du questionnaires :</label>
                        <div class="control">
                            <input class="input" type="text" placeholder="par exemple XXXXXX" required>
                        </div>
                    </div>
                    <button type="submit" class="button" id="bouton-valider">
                        <p>Valider</p>
                    </button>
                </section>
            </form>
        </div>
    </div>
    <div class="is-flex is-justify-content-center pt-5">
        <table class="table is-hoverable" style="width: 95%;">
            <colgroup>
                    <col style="width:33%;">
                    <col style="width:33%;">
                    <col style="width:33%;">
            </colgroup>
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Auteur</th>
                    <th>Temps</th>
                </tr>
            </thead>
            <tbody>
                <?php date_default_timezone_set('Europe/Paris'); ?>
                <?php foreach ($questionnaires as $questionnaire): ?> 
                    <?php 
                        $dateBrute = isset($questionnaire['date_expiration']) ? $questionnaire['date_expiration'] : "";
                        $tempsUnix = strtotime($dateBrute);

                        $date = date("d/m/Y", $tempsUnix);
                        $nbJoursRestant = round(($tempsUnix - strtotime("now")) / 86400, 0);
                    ?>
                    <tr onclick="window.location.href ='./?c=questionnaire&a=repondre&id=<?php echo $questionnaire['id']; ?>';">
                        <td><?php echo htmlspecialchars($questionnaire['titre']); ?></td>
                        <td><?php echo htmlspecialchars($questionnaire['id_createur']); ?></td>
                        <td title="<?php echo htmlspecialchars($date) ?>" style="<?php if ($nbJoursRestant < 7) echo "color : red;" ?>"><?php echo htmlspecialchars($nbJoursRestant); ?> jours restant</td> 
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php if (count($questionnaires) === 0) { ?>
            <p>Aucun questionnaire en attente.</p>
        <?php }?>
    </div>
</main>
