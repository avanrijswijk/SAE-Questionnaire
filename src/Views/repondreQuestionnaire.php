<?php

use App\Controllers\ChoixPossibleController;
use App\Controllers\QuestionnaireController;
$Choix_possibleController = new ChoixPossibleController();

// --- TESTS SUR LES DROITS D'ACCÈS AU QUESTIONNAIRE ---

require_once 'config.php';

if (!isset($questionnaire)) {
	echo '<p>Questionnaire non fourni.</p>';
	return;
}

$q = $this->questionnaireModel->getQuestionnaire($_GET['id']);

$estCreateur = (isset($_SESSION['cas_user']) && $_SESSION['cas_user'] === $q['id_createur']);


$titre = isset($questionnaire['titre']) ? htmlspecialchars($questionnaire['titre']) : 'Sans titre';
$date_exp = isset($questionnaire['date_expiration']) ? htmlspecialchars($questionnaire['date_expiration']) : '';
$code = isset($questionnaire['code']) ? htmlspecialchars($questionnaire['code']) : '';
$auteurNom = $createur['nom'] ?? '';
$auteurPrenom = $createur['prenom'] ?? '';
$auteurNomPrenom = trim($auteurPrenom . ' ' . $auteurNom);
$auteurAffichage = $auteurNomPrenom !== ''
    ? $auteurNomPrenom
    : ($questionnaire['id_createur'] ?? '');
$nbContext = 0

?>
<main class="container" style="margin-top: 25px;">
    <script>
        document.title = "Quit - Répondre à un questionnaire"
    </script>
    <script src="./src/Views/js/repondre_questionnaire/modals/modal.js"></script>
	<?php if ($date_exp): ?>
        <?php 
            $dateBrute = isset($date_exp) ? $date_exp : "";
            $tempsUnix = strtotime($dateBrute);

            $date = date("d/m/Y", $tempsUnix);
            $difference = $tempsUnix - strtotime("now");
            $nbJoursRestant = round($difference / 86400, 0);
            $nbHeureRestant = round(($difference / 3600), 0);
        ?>
        <p title="<?php echo htmlspecialchars($date) ?>"><strong>Expiration :</strong> <?php echo $nbJoursRestant ? $nbJoursRestant . " jours restant" : $nbHeureRestant . " heures restant"; ?></p>
    <?php endif; ?>
	<?php if ($code): ?><p><strong>Code :</strong> <?php echo $code; ?></p><?php endif; ?>
    <?php if ($auteurAffichage): ?><p><strong>Auteur :</strong> <?php echo htmlspecialchars($auteurAffichage); ?></p><?php endif; ?>
    
    <div class="questionnaire-container" style="justify-items: center;">
        <?php if ($estCreateur): ?>
                        
            <div class="notification is-warning is-light mr-4" style="margin-bottom: 10; padding: 0.5rem 1rem;">
                <strong>Mode Aperçu</strong> : Vous êtes le créateur de ce questionnaire.
            </div>
        <?php endif; ?>

        <h1 class="title is-1 mt-3"><?php echo $titre; ?></h1>

        <?php if (empty($questions)): ?>
            <p>Aucune question pour ce questionnaire.</p>
        <?php else: ?>
            <style>
                form {
                    max-width: 90%;
                    width: 90%;
                    min-width: 350px;
                }
            </style>
            <form method="post" action="?c=questionnaire&a=enregistrer-reponses">
                <?php foreach ($questions as $index => $q): ?>

                        <?php
                        // Préparation des données de la question
                        $intitule = isset($q['intitule']) ? htmlspecialchars($q['intitule']) : 'Question sans texte';
                        $type = isset($q['type']) ? $q['type'] : "textfield";
                        $required = isset($q['est_obligatoire']) ? $q['est_obligatoire'] : 0;

                        // Liste des choix possibles
                        $choix = $Choix_possibleController->getChoixDeQuestion($q['id']);

                        // Nom logique du groupe pour radios/checkboxes
                        $groupName = 'question-' . (isset($q['id']) ? $q['id'] : $index);
                        ?>
                    <hr />
                    <div class="question-block" style="<?php if ($type != "context") {echo "padding: 25px 0;";} ?>">
                        <?php if ($type == "context") {
                            $nbContext++?>
                            <p class="subtitle is-4"><?php echo $intitule; ?></p>
                        <?php } else {?>
                            <label for="<?php echo $q["id"]; ?>" class="subtitle is-4"><strong><?php echo ($index+1-$nbContext).'. '; ?></strong><?php echo $intitule; ?> <?php if ($required) echo '<span style="color:red">*</span>'; ?></label>
                        <?php }?>
                        <div class="answer mt-3">
                            <?php switch ($type):
                                default:
                                case "textfield": ?>
                                    <?php 
                                        if (!empty($choix) && isset($choix[0]['id'])) {
                                            $id_choix = $choix[0]['id'];
                                            $name = 'choix-'.$id_choix;
                                        } else {
                                            echo "<p style='color:red'>Erreur : aucun choix associé à ce champ texte.</p>";
                                            break;
                                        }
                                    ?>
                                    <textarea 
                                        name="<?php echo $name; ?>"
                                        <?php if ($required) echo "required"; ?> 
                                        <?php if ($estCreateur) echo "disabled"; ?>
                                        class="textarea" 
                                        placeholder="Remplir ce champ..." 
                                        cols="50"
                                        rows="2" 
                                        maxlength="1800"
                                        style="min-height: 50px; max-height:150px;"></textarea>
                                <?php break; ?>

                                <?php case "long_textfield": ?>
                                    <?php 
                                        if (!empty($choix) && isset($choix[0]['id'])) {
                                            $id_choix = $choix[0]['id'];
                                            $name = 'choix-'.$id_choix;
                                        } else {
                                            echo "<p style='color:red'>Erreur : aucun choix associé à ce champ texte.</p>";
                                            break;
                                        }
                                    ?>
                                    <textarea 
                                        name="<?php echo $name; ?>" 
                                        <?php if ($required) echo "required"; ?> 
                                        <?php if ($estCreateur) echo "disabled"; ?>
                                        class="textarea" 
                                        placeholder="Remplir ce champ..." 
                                        cols="50" 
                                        rows="5" 
                                        maxlength="6000" 
                                        style="min-height: 50px; max-height:300px;"></textarea>
                                <?php break; ?>

                                <?php case "radio": ?>
                                    <div class="radios is-flex is-flex-direction-column" style="row-gap: 0.5em;">
                                        <?php foreach($choix as $reponse):
                                            // n'affiche pas une réponse si son text est null
                                            if (!is_null($reponse) && !is_null($reponse['texte'])) {?>
                                            <label class="radio">
                                                <input
                                                    type="radio"
                                                    class="radio-choice"
                                                    name="<?php echo $groupName; ?>"  
                                                    data-idchoix="<?php echo $reponse['id']; ?>"
                                                    data-texte="<?php echo htmlspecialchars($reponse['texte']); ?>"
                                                    <?php if ($required) echo "required"; ?>
                                                    <?php if ($estCreateur) echo "disabled"; ?>
                                                >
                                                <?php echo htmlspecialchars($reponse["texte"]); ?>
                                            </label>
                                        <?php
                                            } else { ?>
                                            <script>
                                                console.warn(`Le choix[id:<?php echo $q['id']; ?>] de la question[id:<?php echo $reponse['id']; ?>] vaut null`);
                                            </script>
                                            <?php }
                                            endforeach; ?>
                                    </div>
                                <?php break; ?>

                                <?php case "check": ?>
                                    <div class="checkboxs is-flex is-flex-direction-column" style="row-gap: 0.5em;">
                                        <?php foreach($choix as $reponse): 
                                            // n'affiche pas une réponse si son text est null
                                            if (!is_null($reponse) && !is_null($reponse['texte'])) {?>
                                            <label class="checkbox">
                                                <input
                                                    type="checkbox"
                                                    class="check-choice"  
                                                    name="<?php echo $groupName; ?>" 
                                                    data-idchoix="<?php echo $reponse['id']; ?>"
                                                    data-texte="<?php echo htmlspecialchars($reponse['texte']); ?>"
                                                    data-required="<?php echo $required ? 'required' : ''; ?>"
                                                    <?php if ($estCreateur) echo "disabled"; ?>
                                                >
                                                <?php echo htmlspecialchars($reponse["texte"]); ?>
                                            </label>
                                        <?php
                                            } else { ?>
                                            <script>
                                                console.warn(`Le choix[id:<?php echo $q['id']; ?>] de la question[id:<?php echo $reponse['id']; ?>] vaut null`);
                                            </script>
                                            <?php }
                                            endforeach; ?>
                                    </div>
                                <?php break; ?>

                                <?php case "select": ?>
                                    <p><em>Liste de selection - options non disponibles dans la vue.</em></p>
                                <?php break ?>

                                <?php case "context": ?>
                                <?php break ?>
                            <?php endswitch; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <hr />

                <div class="buttons" style="display: flex; justify-content: center; margin-bottom: 50px;">
                    <?php if ($estCreateur): ?>
                        
                        <a href="./?c=questionnaire&a=lister" class="button is-link">
                            <span class="icon"><i class="fas fa-arrow-left"></i></span>
                            <span>Retour à la liste des questionnaires</span>
                        </a>

                    <?php else: ?>
                        
                        <button type="button" id="cancelBtn" class="button is-danger">Annuler</button>
                    <button type="button" id="submitBtn" class="button is-primary" data-brouillon="<?= (int)$questionnaire['brouillon']; ?>">Soumettre</button>
                        
                    <?php endif; ?>
                </div>
                
                <div id="submitModal" class="modal-overlay" role="dialog" style="display: none;">
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
