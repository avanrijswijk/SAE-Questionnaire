<?php

use App\Controllers\AcceptesController;
use App\Controllers\QuestionController;
use App\Models\Acceptes;
use App\Models\Questionnaire;
$questionnaireBDD = new Questionnaire();
$questionnairesFinis = $questionnaireBDD->getQuestionnairePar(["id_createur" => $_SESSION['cas_user'] , "brouillon" => 1]);
$questionnairesBrouillons = $questionnaireBDD->getQuestionnairePar(["id_createur" => $_SESSION['cas_user'] , "brouillon" => 0]);
?>
<main style="background-color: #EFEFEF; height: 100%; overflow-y: auto;">
    <div class="pt-5">
        <h3 class="is-capitalized title is-3 has-text-weight-semibold pl-5">liste des questionnaires publiés</h3>
        <div id="questionnaires" class="is-flex is-justify-content-center pt-5 pb-2">
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
                            <col style="width:70%;">
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
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($questionnairesFinis as $questionnairefini): ?> 
                            <tr class="ligne-questionnaire" data-id="<?php echo $questionnairefini['id']; ?>">
                                <td class="titre-questionnaire">
                                    <span class="titre-texte"><?php echo $questionnairefini["titre"] ?></span>
                                </td>
                                <td class="has-text-centered"><?php echo $questionnaireBDD->getNombreRepondants($questionnaire['id']); ?></td>
                                <td class="has-text-centered">
                                    <div class="image is-32x32 mx-auto" onclick="event.stopPropagation();alert('téléchargement en cours'); window.location.href = './?c=questionnaire&a=exporter&id=<?php echo $questionnaire['id']; ?>';">
                                        <img src="./src/Views/img/telecharger-64.png" alt="icon de téléchargement" title="Télécharger">
                                    </div>
                                </td>
                                <td class="has-text-centered">
                                    <div class="image is-32x32 mx-auto" onclick="event.stopPropagation();if (confirm('Êtes-vous sûr de vouloir supprimer le questionnaire \'<?php echo $questionnaire['titre'] ?>\' ?\nCette action est définitive.')) {window.location.href = './?c=questionnaire&a=supprimer&id=<?php echo $questionnaire['id']; ?>';}">
                                        <img src="./src/Views/img/poubelle-64.png" alt="icon de poubelle" title="Supprimer">
                                    </div> 
                                </td> 
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php } ?>
        </div>
        <h3 class="is-capitalized title is-3 has-text-weight-semibold pl-5">liste des brouillons de questionnaires</h3>
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
                            <col style="width:90%;">
                            <col style="width:10%;">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Supprimer</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($questionnairesBrouillons as $questionnaireBrouillon): ?> 
                            <tr class="ligne-questionnaire" data-id="<?php echo $questionnaireBrouillon['id']; ?>">
                                <td class="titre-questionnaire">
                                    <span class="titre-texte"><?php echo $questionnaireBrouillon["titre"] ?></span>
                                </td>
                                <td>
                                    <div class="image is-32x32" onclick="event.stopPropagation();if (confirm('Êtes-vous sûr de vouloir supprimer le questionnaire \'<?php echo $questionnaireBrouillon['titre'] ?>\' ?\nCette action est définitive.')) {window.location.href = './?c=questionnaire&a=supprimer&id=<?php echo $questionnaireBrouillon['id']; ?>';}">
                                        <img src="./src/Views/img/poubelle-64.png" alt="icon de poubelle" title="Supprimer">
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