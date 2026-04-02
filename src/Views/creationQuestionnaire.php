<?php
require_once 'config.php';
$mon_profil = analyserProfilUtilisateur($_SESSION['cas_groupes']);
$mes_choix = genererCiblesAutorisees($mon_profil);
?>
<script>
    window.questionnaire = <?php echo json_encode($questionnaire); ?>;
</script>

<main class="is-flex is-flex-direction-row" style="max-height: 100vh;">
    <script type="module" src="./src/Views/js/creation_questionnaire/initQuestionnaire.js"></script>
    <script type="module" src="./src/Views/js/creation_questionnaire/modals/fermerQuestionnaire/modalFermer.js"></script>
    <script type="module" src="./src/Views/js/creation_questionnaire/modals/ajoutQuestion/modalAjouter.js"></script>
    <script type="module" src="./src/Views/js/creation_questionnaire/modals/modificationQuestion/modalModifier.js"></script>
    <script>
        document.title = "Quit - Page de création"
    </script>
    <div class="is-flex is-flex-direction-column is-justify-content-space-between pb-6" style="background-color: #f6f5f5;width: 30%;">
        <div id="context-menu">
            <ul class="menu">
                <li class="menu-item" id="menu-item-afficher">Afficher</li>
                <li class="menu-separator"></li>
                <li class="menu-item" id="menu-item-ajouter-reponse">Ajouter une réponse</li>
                <li class="menu-item" id="menu-item-modifier">Modifier</li>
                <li class="menu-separator"></li>
                <li class="menu-item" id="menu-item-supprimer">Supprimer</li>
            </ul>
        </div>
        <script type="module" src="./src/Views/js/creation_questionnaire/contextMenu/contextMenu.js"></script>
        <div style="background-color: #F5A320;border-radius: 0 0 100px 0;">
            <button type="button" id="ajouter-question">
                <h3 class="title is-4 m-1 p-2">+ Ajouter une question</h3>
            </button>
        </div>
        <div id="dialog-creer-question" class="modal">
            <div class="modal-background"></div>
            <form class="modal-card" id="form-ajouter-question">
                <header class="modal-card-head">
                    <p class="modal-card-title has-text-weight-semibold">Ajouter une question</p>
                    <button type="button" id="bouton-fermerMAQ" class="delete" aria-label="close"></button>
                </header>
                <section class="modal-card-body">
                    <div class="field mb-4">
                        <label for="titre-question" class="label">Libellé de la question</label>
                        <div class="control">
                            <textarea class="textarea" rows="2" name="libelle-question" autocapitalize="sentences" autofocus required placeholder="Saisissez votre question ici..."></textarea>
                        </div>
                    </div>
                    
                    <div class="columns mb-0">
                        <div class="column field">
                            <label class="label">Type de la question</label>
                            <div class="control" id="radios-types">
                                <label class="radio is-block mb-2"><input type="radio" name="type-question" value="champs-libre" id="radio-champs-libre" checked required> Champ libre</label>
                                <label class="radio is-block mb-2"><input type="radio" name="type-question" value="radio-box" id="radio-radio-box"> Boutons radio (1 seul choix)</label>
                                <label class="radio is-block mb-2"><input type="radio" name="type-question" value="check-box" id="radio-check-box"> Cases à cocher (Multiples)</label>
                                <label class="radio is-block"><input type="radio" name="type-question" value="context" id="radio-context"> Mise en contexte (Texte seul)</label>
                            </div>
                        </div> 
                        
                        <div class="column field" id="radio-sous-type">
                            <label class="label">Taille du champ :</label>
                            <div class="control" id="sous-type-champs">
                                <label class="radio is-block mb-2"><input type="radio" name="sous-type-question" value="champs-libre-court" checked required> Petit champ (1 ligne)</label>
                                <label class="radio is-block"><input type="radio" name="sous-type-question" value="champs-libre-long"> Grand champ (Paragraphe)</label>
                            </div>
                        </div>
                    </div>

                    <hr class="mt-2 mb-4">

                    <div id="obligatoire" class="field">
                        <div class="control">
                            <label class="checkbox has-text-weight-semibold">
                                <input type="checkbox" name="question-obligatoire" value="obligatoire" class="mr-2">
                                Rendre cette question obligatoire
                            </label>
                        </div>
                    </div>
                </section>
                <footer class="modal-card-foot buttons is-centered">
                    <button type="submit" class="button is-warning has-text-weight-bold px-6" id="bouton-validerMAQ">Créer la question</button>
                </footer>
            </form>
        </div>
        <div id="dialog-modifier-question" class="modal">
            <div class="modal-background"></div>
            <form class="modal-card" id="form-modifier-question">
                <header class="modal-card-head">
                    <p class="modal-card-title">Modifier une question</p>
                    <button type="button" id="bouton-fermerMMQ" class="delete" aria-label="close"></button>
                </header>
                <section class="modal-card-body">
                    <div class="field">
                        <label for="libelle-question" class="label">Libellé de la question :</label>
                        <div class="control">
                            <textarea class="textarea" rows=2 name="libelle-question-modifier" autocapitalize="sentences" autofocus required></textarea>
                        </div>
                    </div>
                    <div id="sous-type-modifier" class="field">
                        <label class="label">Sous type :</label>
                        <div class="radios" id="sous-type-champ-modifier">
                            <label class="radio is-unselectable">
                                <input type="radio" name="sous-type-question-c-modifier" value="champs-libre-court" checked required>
                                Petit champs
                            </label>
                            <label class="radio is-unselectable">
                                <input type="radio" name="sous-type-question-c-modifier" value="champs-libre-long">
                                Grand champs
                            </label>
                        </div>
                        <div class="radios" id="sous-type-choix-multiples-modifier">
                            <label class="radio is-unselectable">
                                <input type="radio" name="sous-type-question-cm-modifier" value="radio-box" checked required>
                                Raido box
                            </label>
                            <label class="radio is-unselectable">
                                <input type="radio" name="sous-type-question-cm-modifier" value="check-box">
                                Check box
                            </label>
                        </div>
                    </div>

                    <hr class="mt-4 mb-3">

                    <div id="obligatoire-modifier" class="field">
                        <label for="titre-question" class="label">Option :</label>
                        <div class="checkboxes">
                            <label class="checkbox is-unselectable">
                                <input type="checkbox" name="question-obligatoire-modifier" value="obligatoire">
                                Question obligatoire
                            </label>
                        </div>
                    </div>
                    
                    
                </section>
                <footer class="modal-card-foot buttons" style="justify-content: center;">
                    <button type="submit" class="button is-success" id="bouton-validerMMQ"  style="width: 20%;">
                        <p>Modifier</p>
                    </button>
                    <button type="button" class="button is-danger" id="bouton-annulerMMQ"  style="width: 20%;">
                        <p>Annuler</p>
                    </button>
                </footer>
            </form>
        </div>
        <div id="visualiseur-questions" class="pl-2 pr-3" style="height: 100%; max-height: 46em; overflow-y: scroll;">
        </div>
        <div id="dialog-finir-questionnaire" class="modal">
            <div class="modal-background"></div>
            <form id="form-enregistrer" class="modal-card" action="?c=questionnaire&a=enregistrer-creation" method="post">
                <header class="modal-card-head">
                    <p class="modal-card-title">Enregistrer le questionnaire</p>
                    <button type="button" id="bouton-fermer" class="delete" aria-label="close"></button>
                </header>
                <section class="modal-card-body is-flex is-flex-direction-column is-align-items-center">
                    <div class="field" style="width: 60%;">
                        <label class="label" for="nom-questionnaire">Nom du Questionnaire :</label>
                        <div class="control">
                            <input class="input" name="nom-questionnaire" id="nom-questionnaire" required></input> 
                        </div>     
                    </div>
                    <div class="field" style="width: 60%;">
                        <label class="label" for="date-expriration">Date d'expiration :</label>
                        <?php 
                            date_default_timezone_set('Europe/Paris'); 
                            $demain = date('Y-m-d', strtotime('+1 day'));
                            $date_max = date('Y-m-d', strtotime('+3 year'));
                        ?>
                        <input type="date" class="input" name="date-expriration" id="date-expriration" min="<?php echo $demain; ?>" value="<?php echo $demain; ?>" max="<?php echo $date_max; ?>"/>
                    </div>
                    <div class="field" style="width: 60%;">
                        <label class="label" for="mes-cibles">Qui peut répondre :</label>
                        <select name="groupes_cibles[]" id="mes-cibles" multiple="multiple" required>
                        
                            <?php
                            foreach ($mes_choix as $code_cas => $nom_propre) {
                                echo "<option value=\"" . htmlspecialchars($code_cas) . "\">" . htmlspecialchars($nom_propre) . "</option>";
                            }
                            ?>
                            
                        </select>
                    </div>
                    <div class="field" style="width: 60%;">
                        <p class="pt-3" style="color: #920b0b;">* Le code d'accès au questionnaire sera généré après sa création</p>  
                    </div>
                </section>
                <footer class="modal-card-foot" style="justify-content: center;">
                    <input type="hidden" name="mode_enregistrement" id="mode-enregistrement" value="">
                    <input type="hidden" name="id_questionnaire" id="id-questionnaire" value="<?php echo isset($questionnaire['id']) ? htmlspecialchars($questionnaire['id']) : ''; ?>">
                    <button type="button" class="button mr-4 has-background-warning" id="bouton-brouillonMVQ">
                        <p>Brouillon</p>
                    </button>
                    <button type="button" class="button has-background-success" id="bouton-PublierMVQ">
                        <p>Publier</p>
                    </button>
                </footer>
            </form>
        </div>
        <div class="p-3 is-flex is-justify-content-center">
            <button type="button" id="bouton-finir" class="button is-large has-text-weight-bold" style="border: 2px solid #11841e; border-radius: 12px; transition: all 0.2s ease;">
                <span class="icon is-medium mr-2 ml-1"><i class="fas fa-check"></i></span>
                <span class="mr-4">FINIR</span>
            </button>
        </div>
    </div>
    <div style="background-color: #ffe7c0; width:100%; line-break: anywhere;"  class="is-flex is-justify-content-center">
        <div class="mt-5 mb-5" style="background-color: rgb(248, 248, 248); width:85%; padding: 25px 15px; overflow: auto;" id="visualiseur-questionnaire">
        </div>
    </div>
    <div id="notifications" style="width:30%; position: fixed; bottom: 2%; left: 2%; max-height: 50%;">
    </div>
    <script type="module" src="./src/Views/js/creation_questionnaire/notification/notification.js"></script>
</main>


<!-- Code pour l'affichage et le style du choix des groupes cibles -->
<script>
$(document).ready(function() {
    
    $('#mes-cibles').select2({
        placeholder: "🔎 Cliquez pour choisir un groupe...",
        language: "fr",
        allowClear: true,
        width: '100%'
    });

});
</script>
<style>
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #dbdbdb; border-radius: 4px; min-height: 2.5em;
    }
        .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2); /* Halo bleu */
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #eef6fc; color: #2160c4; border: none; 
    }
</style>