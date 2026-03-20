<?php
require_once 'config.php';
$mon_profil = analyserProfilUtilisateur($_SESSION['cas_groupes']);
$mes_choix = genererCiblesAutorisees($mon_profil);

// Extraction des groupes actuels du questionnaire
$json_regles = $questionnaire['groupes_autorises'] ?? '{}';
$regles = json_decode($json_regles, true);

// On récupère les groupes actuels, sinon on crée un tableau vide
$groupes_actuels = $regles['groupes_requis'] ?? [];
?>

<body class="has-background-white-ter">

<div class="container mt-6 mb-6">
    <div class="box">
        <h1 class="title is-3 mb-5 has-text-centered">
            Modifier le questionnaire
        </h1>

        <form action="?c=questionnaire&a=enregistrer" method="POST">
            
            <input type="hidden" name="id" value="<?php echo $questionnaire['id']; ?>">

            <div class="field mb-4">
                <label class="label">Titre du questionnaire</label>
                <div class="control">
                    <input class="input" type="text" name="nom-questionnaire" 
                           value="<?php echo htmlspecialchars($questionnaire['titre'] ?? ''); ?>" required>
                </div>
            </div>

            <div class="field mb-4">
                <label class="label">Date d'expiration</label>
                <div class="control">
                    <input class="input" type="date" name="date-expriration" 
                           value="<?php echo htmlspecialchars(substr($questionnaire['date_expiration'], 0, 10)); ?>" required>
                </div>
            </div>

            <div class="field mb-5">
                <label class="label">Qui peut répondre ?</label>
                <div class="control">
                    <select class="input" name="groupes_cibles[]" id="mes-cibles" multiple="multiple" required>
                        <?php
                            foreach ($mes_choix as $code_cas => $nom_propre) {
                                $selected = in_array($code_cas, $groupes_actuels) ? 'selected' : '';
                                echo "<option value=\"" . htmlspecialchars($code_cas) . "\" $selected>" . htmlspecialchars($nom_propre) . "</option>";
                            }
                        ?>
                    </select>
                </div>
            </div>

            <div class="field is-grouped is-grouped-centered mt-5">
                <div class="control">
                    <button type="submit" class="button is-warning">
                        <span class="icon"><i class="fas fa-save"></i></span>
                        <span>Mettre à jour</span>
                    </button>
                </div>
                <div class="control">
                    <a href="./?c=questionnaire&a=detail&id=<?php echo $questionnaire['id']; ?>" class="button is-light">Annuler</a>
                </div>
            </div>

        </form>
    </div>
</div>


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