<?php

use App\Controllers\AcceptesController;
use App\Controllers\QuestionController;
use App\Models\Acceptes;
use App\Models\Questionnaire;
$questionnaireBDD = new Questionnaire();
$questionnaires = $questionnaireBDD->getQuestionnairePar(["id_createur" => 1]);
?>
<main style="background-color: #EFEFEF; height: 100%; overflow-y: auto;">
    <div class="pt-5">
        <h3 class="is-capitalized title is-3 has-text-weight-semibold pl-5">liste des questionnaires</h3>
        <div id="questionnaires" class="is-flex is-justify-content-center pt-5 pb-2">
            <?php if (count($questionnaires) < 1) { ?>
                <span><?php echo "Aucun questionnaire"; ?></span>
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
                            <th>Réponses</th>
                            <th>Telecharger</th>
                            <th>Supprimer</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($questionnaires as $questionnaire): ?> 
                            <tr class="ligne-questionnaire" data-id="<?php echo $questionnaire['id']; ?>">
                                <td class="titre-questionnaire">
                                    <span class="titre-texte"><?php echo $questionnaire["titre"] ?></span>
                                </td>
                                <td><?php //echo ($acceptesController->nombreReponduText($questionnaire["id"]))?></td> <!-- renvoi rien -->
                                <td>
                                    <div class="image is-32x32 ml-1 mr-2" onclick="event.stopPropagation();alert('téléchargement en cours'); window.location.href = './?c=questionnaire&a=exporter&id=<?php echo $questionnaire['id']; ?>';">
                                        <img src="./src/Views/img/telecharger-64.png" alt="icon de téléchargement" title="Télécharger">
                                    </div>
                                </td>
                                <td>
                                    <div class="image is-32x32" onclick="event.stopPropagation();alert('suppression effectée'); window.location.href = './?c=questionnaire&a=supprimer&id=<?php echo $questionnaire['id']; ?>';">
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