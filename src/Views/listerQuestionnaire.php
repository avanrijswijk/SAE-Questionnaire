<main style="background-color: #EFEFEF; height: 100%;">
    <div>
        <script src="./src/Views/js/modal_trouver_questionnaire.js"></script>
        <button style="background-color: #F5A320;border-radius: 0 0 100px 0;" id="bouton-code">
            <h3 class="title is-4 p-2 pr-6 is-3 has-text-weight-semibold">J'ai un code</h3>
        </button>
        <div id="dialog" class="modal">
            <div class="modal-background"></div>
            <form id="form-enregistrer" class="modal-card">
                <section class="modal-card-body" style="border-radius: 15px;">
                    <button id="bouton-fermer" class="delete" aria-label="close" style="position: absolute; right:10px; top: 10px;"></button>
                    <div class="field">
                        <label class="label">Code du questionnaies :</label>
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
    <div class="p-6">
        <h3 class="is-capitalized title is-3 has-text-weight-semibold">liste des questionnaires en attente</h3>
        <div class="is-flex is-flex-direction-row is-justify-content-space-between p-2 mb-2">
            <p>Titre du questionnaire</p>
            <p>Auteur</p>
            <p>Temps</p>
        </div>

        <div id="questionnaires">
            <?php if (count($questionnaires) === 0) { ?>
                <p>Aucun questionnaire en attente.</p>
            <?php } else { ?>
                <?php foreach ($questionnaires as $questionnaire): ?>
                    <a href="./?c=questionnaire&a=#######&id=<?php echo $questionnaire['id']; ?>" class="is-flex is-flex-direction-row is-justify-content-space-between p-2 mb-2" style="border: 1px solid black; background-color: #ffffff;">
                        <p style="color: black"><?php echo htmlspecialchars($questionnaire['titre']); ?></p>
                        <p style="color: black"><?php echo htmlspecialchars($questionnaire['id_createur']); ?></p>
                        <p style="color: black"><?php echo htmlspecialchars($questionnaire['date_expiration']); ?></p>
                    </a>
                <?php endforeach; ?>
            <?php } ?>
            
            <style>
                div#questionnaires div:hover {
                    border: #c70000ff;
                }
            </style>

            <!-- Exemple statique
            <a class="is-flex is-flex-direction-row is-justify-content-space-between p-2 mb-2" style="border: 1px solid black; background-color: #ffffff;">
                <p style="color: black">Titre du questionnaire</p>
                <p style="color: black">Auteur</p>
                <p style="color: black">Temps</p>
            </a>
             -->
            
        </div>
    </div>
</main>