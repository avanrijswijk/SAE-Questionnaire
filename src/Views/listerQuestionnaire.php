<?php
    use App\Models\Reponses_utilisateur;
    $reponsesModele = new Reponses_utilisateur();
?>

<main style="background-color: #EFEFEF; min-height: 100vh; overflow-y: auto;">
    <script>
        document.title = "Quit - Liste des questionnaires"
    </script>
    <div>
        <script src="./src/Views/js/lister_questionnaire/modals/code/modalCode.js"></script>
        <button style="background-color: #F5A320;border-radius: 0 0 100px 0;" id="bouton-code">
            <h3 class="title is-4 p-2 pr-6 is-3 has-text-weight-semibold">J'ai un code</h3>
        </button>
        <div id="dialog-code" class="modal">
            <div class="modal-background"></div>
            <form id="form-enregistrer" class="modal-card" action="./?c=questionnaire&a=acceder-par-code" method="POST">
                <section class="modal-card-body" style="border-radius: 15px;">
                    <button type="button" id="bouton-fermer" class="delete" aria-label="close" style="position: absolute; right:10px; top: 10px;"></button>
                    <div class="field">
                        <label class="label">Code du questionnaire :</label>
                        <div class="control">
                            <input class="input" name="code" type="text" placeholder="Exemple : XXXX" minlength="4" maxlength="4" pattern="[A-Za-z]{4}" required>
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
                <col style="width:30%;">
                <col style="width:30%;">
                <col style="width:25%;">
                <col style="width:15%;">
            </colgroup>
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Auteur</th>
                    <th>Temps</th>
                    <th class="has-text-centered">Statut</th> </tr>
            </thead>
            <tbody id="table">
                <?php date_default_timezone_set('Europe/Paris'); ?>
                <?php foreach ($questionnaires as $questionnaire): ?> 
                    <?php 
                        $dateBrute = isset($questionnaire['date_expiration']) ? $questionnaire['date_expiration'] : "";
                        $tempsUnix = strtotime($dateBrute);

                        $date = date("d/m/Y", $tempsUnix);
                        $difference = $tempsUnix - strtotime("now");
                        $nbJoursRestant = round($difference / 86400, 0);
                        $nbHeureRestant = round(($difference / 3600), 0);
                        
                        $aDejaRepondu = $reponsesModele->aDejaRepondu($questionnaire['id'], $_SESSION['cas_user']);
                    ?>
                    <?php if ($nbJoursRestant > 0) : ?>
                        <tr onclick="window.location.href ='./?c=questionnaire&a=repondre&id=<?php echo $questionnaire['id']; ?>';" style="cursor: pointer;">
                            <td><?php echo htmlspecialchars($questionnaire['titre']); ?></td>
                            <?php
                                $createurNom = $questionnaire['createur_nom'] ?? '';
                                $createurPrenom = $questionnaire['createur_prenom'] ?? '';
                                $createurNomPrenom = trim($createurPrenom . ' ' . $createurNom);
                                $createurAffichage = $createurNomPrenom !== ''
                                    ? $createurNomPrenom
                                    : ($questionnaire['id_createur'] ?? '');
                            ?>
                            <td><?php echo htmlspecialchars($createurAffichage); ?></td>
                            
                            <?php if ($nbJoursRestant <= 0) : ?>
                                <td title="<?php echo htmlspecialchars($date) ?>" style="color : red;"><?php echo htmlspecialchars($nbHeureRestant); ?> heures restant</td> 
                            <?php else : ?>
                                <td title="<?php echo htmlspecialchars($date) ?>" style="<?php if ($nbJoursRestant < 7) echo "color : red;" ?>"><?php echo htmlspecialchars($nbJoursRestant); ?> jours restant</td> 
                            <?php endif ?>

                            <td class="has-text-centered">
                                <?php if ($aDejaRepondu): ?>
                                    <span class="tag is-success is-light is-medium">
                                        <i class="fas fa-check mr-2"></i> Fait
                                    </span>
                                <?php else: ?>
                                    <span class="tag is-warning is-light is-medium">
                                        <i class="fas fa-clock mr-2"></i> À faire
                                    </span>
                                <?php endif; ?>
                            </td>

                        </tr>
                    <?php endif ?>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                const tbody = document.getElementById("table");
                const tableElement = tbody.closest("table");
                
                if (tbody && tbody.childElementCount == 0) {
                    tableElement.style.display = "none";
                    
                    const emptyState = document.createElement("div");
                    emptyState.className = "has-text-centered mt-6 mb-6";
                    emptyState.innerHTML = `
                        <div class="mb-4">
                            <span class="icon has-text-grey-light" style="font-size: 4rem; height: 4rem; width: 4rem;">
                                <i class="fas fa-inbox"></i>
                            </span>
                        </div>
                        <h2 class="title is-4 has-text-grey">Aucun questionnaire disponible</h2>
                        <p class="subtitle is-6 has-text-grey-light mt-2">Vous n'avez pas de questionnaire en attente pour le moment.</p>
                    `;
                    document.querySelector("div.is-flex.is-justify-content-center.pt-5").appendChild(emptyState);
                }
            });
        </script>
    </div>
    <div id="notifications" style="width:30%; position: fixed; bottom: 2%; left: 2%; max-height: 50%;"></div>
</main>