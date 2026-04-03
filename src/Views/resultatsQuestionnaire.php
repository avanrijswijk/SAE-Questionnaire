<?php

use App\Models\Questionnaire;
$questionnaireBDD = new Questionnaire();
$questionnairesFinis = $questionnaireBDD->getQuestionnairePar(["id_createur" => $_SESSION['cas_user'] , "brouillon" => 1]);
$questionnairesBrouillons = $questionnaireBDD->getQuestionnairePar(["id_createur" => $_SESSION['cas_user'] , "brouillon" => 0]);
?>
<main style="background-color: #EFEFEF; min-height: calc(100vh - 65px); padding-bottom: 5rem;">    <script>
        document.title = "Quit - Mes Questionnaires"
    </script>
    <div class="pt-5">
        <h3 class="is-capitalized title is-3 has-text-weight-semibold pl-5 mb-1">Questionnaires Publiés</h3>
        <div id="questionnaires" class="is-flex is-justify-content-center pt-5 pb-2 mb-6">
            <?php if (count($questionnairesFinis) < 1) { ?>
                <span><?php echo "Aucun questionnaire publié"; ?></span>
            <?php } else { ?>
                <style>
                    td {
                        text-align: center;
                    }

                    td:first-child {
                        text-align: left;
                    }
                </style>
                <table class="table is-hoverable" style="width: 95%;">
                    <colgroup>
                            <col style="width:55%;">
                            <col style="width:15%;">
                            <col style="width:10%;">
                            <col style="width:10%;">
                            <col style="width:10%;">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th class="has-text-centered">Réponses</th>
                            <th class="has-text-centered">Telecharger</th>
                            <th class="has-text-centered">Supprimer</th>
                            <th class="has-text-centered"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($questionnairesFinis as $questionnairefini): 
                            $idQuestionnaire = $questionnairefini['id'];
                            $titreQuestionnaire = $questionnairefini['titre'];

                            $dateBrute = $questionnairefini['date_expiration'] ?? '';
                            $estExpire = false;
                            if (!empty($dateBrute)) {
                                if (strtotime($dateBrute) < time()) {
                                    $estExpire = true;
                                }
                            }
                            ?> 
                            <tr class="ligne-questionnaire" data-id="<?php echo $idQuestionnaire; ?>">
                                <td class="titre-questionnaire">
                                    <span class="titre-texte"><?php echo $titreQuestionnaire ?></span>
                                    <?php if ($estExpire): ?>
                                        <span class="tag is-danger is-light ml-3">Expiré</span>
                                    <?php else: ?>
                                        <span class="tag is-success is-light ml-3">Actif</span>
                                    <?php endif; ?>
                                </td>

                                <td class="has-text-centered"><?php echo $questionnaireBDD->getNombreRepondants($idQuestionnaire); ?></td>

                                <td class="has-text-centered">
                                    <div class="image is-32x32 mx-auto" onclick="event.stopPropagation();alert('téléchargement en cours'); window.location.href = './?c=questionnaire&a=exporter&id=<?php echo $idQuestionnaire; ?>';">
                                        <img src="./src/Views/img/telecharger-64.png" alt="icon de téléchargement" title="Télécharger">
                                    </div>
                                </td>

                                <td class="has-text-centered">
                                    <div class="image is-32x32 mx-auto" onclick="event.stopPropagation();if (confirm('Êtes-vous sûr de vouloir supprimer le questionnaire \'<?php echo htmlspecialchars(addslashes($titreQuestionnaire), ENT_QUOTES); ?>\' ?\nCette action est définitive.')) {window.location.href = './?c=questionnaire&a=supprimer&id=<?php echo $idQuestionnaire; ?>';}">
                                        <img src="./src/Views/img/poubelle-64.png" alt="icon de poubelle" title="Supprimer"> 
                                    </div> 
                                </td> 

                                <td class="has-text-centered">
                                    <div class="dropdown is-right" id="dropdown-publie-<?php echo $idQuestionnaire; ?>">
                                        <div class="dropdown-trigger">
                                            <button class="button is-white is-small action-btn" aria-haspopup="true" aria-controls="dropdown-menu-<?php echo $idQuestionnaire; ?>" onclick="toggleDropdown(event, 'dropdown-publie-<?php echo $idQuestionnaire; ?>')">
                                                <span class="icon is-small has-text-black">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </span>
                                            </button>
                                        </div>
                                        <div class="dropdown-menu" id="dropdown-menu-<?php echo $idQuestionnaire; ?>" role="menu" style="min-width: 8rem;">
                                            <div class="dropdown-content has-text-left">
                                                <a href="./?c=questionnaire&a=modifier&id=<?php echo $idQuestionnaire; ?>" class="dropdown-item" onclick="event.stopPropagation();">
                                                    <span class="icon is-small mr-2"><i class="fas fa-edit"></i></span> Modifier
                                                </a>
                                                <a href="./?c=questionnaire&a=dupliquer&id=<?php echo $idQuestionnaire; ?>" class="dropdown-item" onclick="event.stopPropagation();">
                                                    <span class="icon is-small mr-2"><i class="fas fa-copy"></i></span> Dupliquer
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php } ?>
        </div>
        <h3 class="is-capitalized title is-3 has-text-weight-semibold pl-5 mb-1">Questionnaires Brouillon</h3>
        <div id="questionnaires" class="is-flex is-justify-content-center pt-5 pb-2">
            <?php if (count($questionnairesBrouillons) < 1) { ?>
                <span><?php echo "Aucun brouillon de questionnaire"; ?></span>
            <?php } else { ?>
                <style>
                    td {
                        text-align: center;
                    }

                    td:first-child {
                        text-align: left;
                    }
                </style>
                <table class="table is-hoverable" style="width: 95%;">
                    <colgroup>
                            <col style="width:80%;">
                            <col style="width:10%;">
                            <col style="width:10%;">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th class="has-text-centered">Supprimer</th>
                            <th class="has-text-centered"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($questionnairesBrouillons as $questionnaireBrouillon): 
                            $idQuestionnaireBrouillon = $questionnaireBrouillon['id'];
                            $titreQuestionnaireBrouillon = $questionnaireBrouillon['titre'];
                            ?> 
                            <tr class="ligne-questionnaire" data-id="<?php echo $idQuestionnaireBrouillon; ?>">
                                <td class="titre-questionnaire">
                                    <span class="titre-texte"><?php echo $titreQuestionnaireBrouillon ?></span>
                                    <span class="tag is-warning is-light ml-3">Brouillon</span>
                                </td>

                                <td class="has-text-centered">
                                    <div class="image is-32x32 mx-auto" onclick="event.stopPropagation();if (confirm('Êtes-vous sûr de vouloir supprimer le questionnaire \'<?php echo htmlspecialchars(addslashes($titreQuestionnaire), ENT_QUOTES); ?>\' ?\nCette action est définitive.')) {window.location.href = './?c=questionnaire&a=supprimer&id=<?php echo $idQuestionnaireBrouillon; ?>';}">
                                        <img src="./src/Views/img/poubelle-64.png" alt="icon de poubelle" title="Supprimer">
                                    </div> 
                                </td>

                                <td class="has-text-centered">
                                    <div class="dropdown is-right" id="dropdown-brouillon-<?php echo $idQuestionnaireBrouillon; ?>">
                                        <div class="dropdown-trigger">
                                            <button class="button is-white is-small action-btn" aria-haspopup="true" aria-controls="dropdown-menu-brouillon-<?php echo $idQuestionnaireBrouillon; ?>" onclick="toggleDropdown(event, 'dropdown-brouillon-<?php echo $idQuestionnaireBrouillon; ?>')">
                                                <span class="icon is-small has-text-black">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </span>
                                            </button>
                                        </div>
                                        <div class="dropdown-menu" id="dropdown-menu-brouillon-<?php echo $idQuestionnaireBrouillon; ?>" role="menu" style="min-width: 8rem;">
                                            <div class="dropdown-content has-text-left">
                                                <a href="./?c=questionnaire&a=modifier&id=<?php echo $idQuestionnaireBrouillon; ?>" class="dropdown-item" onclick="event.stopPropagation();">
                                                    <span class="icon is-small mr-2"><i class="fas fa-edit"></i></span> Modifier
                                                </a>
                                                <a href="./?c=questionnaire&a=dupliquer&id=<?php echo $idQuestionnaireBrouillon; ?>" class="dropdown-item" onclick="event.stopPropagation();">
                                                    <span class="icon is-small mr-2"><i class="fas fa-copy"></i></span> Dupliquer
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php } ?>
        </div>
    </div>
</main>

<script src="./src/Views/js/resultats_questionnaire/afficher/resultat.js"></script>

<script>
    function toggleDropdown(event, dropdownId) {
        event.stopPropagation();
        const targetDropdown = document.getElementById(dropdownId);
        const isActive = targetDropdown.classList.contains('is-active');
        
        fermerTousLesDropdowns();
        
        if (!isActive) {
            targetDropdown.classList.add('is-active');
        }
    }

    function fermerTousLesDropdowns() {
        const dropdowns = document.querySelectorAll('.dropdown');
        dropdowns.forEach(dropdown => {
            dropdown.classList.remove('is-active');
        });
    }

    document.addEventListener('click', () => {
        fermerTousLesDropdowns();
    });
</script>