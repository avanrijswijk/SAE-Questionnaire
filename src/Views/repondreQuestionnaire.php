<?php
if (!isset($questionnaire)) {
	echo '<p>Questionnaire non fourni.</p>';
	return;
}

$titre = isset($questionnaire['titre']) ? htmlspecialchars($questionnaire['titre']) : 'Sans titre';
$date_exp = isset($questionnaire['date_expiration']) ? htmlspecialchars($questionnaire['date_expiration']) : '';
$code = isset($questionnaire['code']) ? htmlspecialchars($questionnaire['code']) : '';
$auteur = isset($questionnaire['id_createur']) ? htmlspecialchars($questionnaire['id_createur']) : '';

?>
<main class="container" style="margin-top: 25px;">
    <script src="./src/Views/js/repondre_questionnaire/modals/modal.js"></script>
	<?php if ($date_exp): ?><p><strong>Expiration :</strong> <?php echo $date_exp; ?></p><?php endif; ?>
	<?php if ($code): ?><p><strong>Code :</strong> <?php echo $code; ?></p><?php endif; ?>
    <?php if ($auteur): ?><p><strong>Auteur :</strong> <?php echo $auteur; ?></p><?php endif; ?>
    
    <div class="questionnaire-container" style="justify-items: center;">
        <h1 class="title is-1 mt-3"><?php echo $titre; ?></h1>

        <?php if (empty($questions)): ?>
            <p>Aucune question pour ce questionnaire.</p>
        <?php else: ?>
            <form method="post" action="">
                <?php foreach ($questions as $index => $q): ?>
                    <?php
                        $intitule = isset($q['intitule']) ? htmlspecialchars($q['intitule']) : 'Question sans texte';
                        $type = isset($q['type']) ? $q['type'] : "textfield";
                        $required = isset($q['est_obligatoire']) ? $q['est_obligatoire'] : 0;
                        $name = 'question-'.(isset($q['id']) ? $q['id'] : $index);
                    ?>
                    <hr />
                    <div class="question-block" style="padding: 25px 0;">
                        <label for="<?php echo $q["id"]; ?>" class="subtitle is-4"><strong><?php echo ($index+1).'. '; ?></strong><?php echo $intitule; ?> <?php if ($required) echo '<span style="color:red">*</span>'; ?></label>
                        <div class="answer">
                            <?php switch ($type):
                                default:
                                case "textfield": ?>
                                    <textarea name="<?php echo $name; ?>" <?php if ($required) echo "required"; ?> class="textarea" placeholder="Remplir ce champ..." cols="50" rows="2" maxlength="1800"></textarea>
                                <?php break; ?>
                                
                                <?php case "long_textfield": ?>
                                    <textarea name="<?php echo $name; ?>" <?php if ($required) echo "required"; ?> class="textarea" placeholder="Remplir ce champ..." cols="50" rows="5" maxlength="6000"></textarea>
                                <?php break ?>

                                <?php case "radio": ?>
                                    <p><em>Choix unique - options non disponibles dans la vue.</em></p>
                                <?php break ?>

                                <?php case "check": ?>
                                    <p><em>Choix multiple - options non disponibles dans la vue.</em></p>
                                <?php break ?>

                                <?php case "select": ?>
                                    <p><em>Liste de selection - options non disponibles dans la vue.</em></p>
                                <?php break ?>
                            <?php endswitch; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <hr />

                <div class="buttons" style="display: flex; justify-content: center; margin-bottom: 50px;">

                    <button type="button" id="cancelBtn" class="button is-danger">Annuler</button>
                    <button type="button" id="submitBtn" class="button is-primary">Soumettre</button>

                </div>
                
                <div id="submitModal" class="modal-overlay" role="dialog" aria-modal="true" aria-hidden="true">
                    <div class="modal-card">
                        <div class="modal-content">
                            <div class="modal-title">Confirmer l'envoi</div>
                            <div class="modal-body">Êtes-vous sûr de vouloir envoyer les informations ? Elles ne pourront plus être modifiées après envoi.</div>
                            <div class="modal-actions">
                                <button type="button" id="submitModalNo" class="button">Annuler</button>
                                <button type="submit" id="submitModalYes" class="button is-primary">Envoyer</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div id="cancelModal" class="modal-overlay" role="dialog" aria-modal="true" aria-hidden="true">
                <div class="modal-card">
                    <div class="modal-content">
                        <div class="modal-title">Quitter la page ?</div>
                        <div class="modal-body">Êtes-vous sûr de vouloir quitter la page ? Les informations saisies ne pourront pas être enregistrées.</div>
                        <div class="modal-actions">
                            <button type="button" id="cancelModalNo" class="button">Annuler</button>
                            <button type="button" id="cancelModalYes" class="button is-danger">Quitter</button>
                        </div>
                    </div>
                </div>
            </div>

        <?php endif; ?>
    </div>
</main>


