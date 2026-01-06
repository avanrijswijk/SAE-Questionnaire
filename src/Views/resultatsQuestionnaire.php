<?php
$questionnaireBDD = new Questionnaire();
$questionnaires = $questionnaireBDD->getQuestionnaireBy(["id_createur" => 1]);
?>
<main style="background-color: #EFEFEF; height: 100%;">
    <script src="./src/Views/js/resultats_questionnaire/afficher/resultat.js"></script>
    <div class="p-6">
        <h3 class="is-capitalized title is-3 has-text-weight-semibold">liste des questionnaires</h3>
        <div class="is-flex is-flex-direction-row is-justify-content-space-between p-2 mb-2">
            <p>Titre du questionnaire</p>
        </div>
        <div id="questionnaires">
            <?php if (count($questionnaires) < 1): ?>
                <span><?php echo "Aucun questionnaire"; ?></span>
            <?php endif ?>

            <?php foreach ($questionnaires as $questionnaire): ?>
                <div class="is-flex is-flex-direction-row is-justify-content-space-between p-2 mb-2" style="border: 1px solid black; background-color: #ffffff; align-items:center;" onclick="alert('affichage ficitif');">
                    <p style="color: black"><?php echo $questionnaire["titre"] ?></p>
                    <div class="is-flex is-flex-direction-row" style="border-left: 1px solid;">
                       <div class="image is-32x32 ml-1 mr-2" onclick="telechargerResultats();">
                            <img src="./src/Views/img/telecharger-64.png" alt="icon de téléchargement" title="Telecharger">
                        </div>
                        <br>
                        <div class="image is-32x32" onclick="alert('suppression ficitif');">
                            <img src="./src/Views/img/poubelle-64.png" alt="icon de poubelle" title="Supprimer">
                        </div> 
                    </div>
                    
                </div>
            <?php endforeach ?>
        </div>
    </div>
</main>