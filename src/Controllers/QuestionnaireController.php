<?php

namespace App\Controllers;

use App\Models\Questionnaire;
use App\Models\Question;
use App\Models\Reponses_utilisateur;
use App\Models\Choix_possible;
use App\Models\Statistique;

require_once 'config.php';


class QuestionnaireController {

    private $questionnaireModel;
    private $questionModel;
    private $reponses_utilisateurModel;
    private $choix_possibleModel;
    private $statistiqueModel;

    /**
     * Constructeur de la classe QuestionnaireController.
     * Initialise les instances des modèles nécessaires.
     */
    public function __construct() {
        $questionnaireModel = new Questionnaire();
        $this->questionnaireModel = $questionnaireModel;
        $questionModel = new Question();
        $this->questionModel = $questionModel;
        $reponses_utilisateurModel = new Reponses_utilisateur();
        $this->reponses_utilisateurModel = $reponses_utilisateurModel;
        $statistiqueModel = new Statistique();
        $this->statistiqueModel = $statistiqueModel;
        $choix_possibleModel = new Choix_possible();
        $this->choix_possibleModel = $choix_possibleModel;
    }

    /**
     * Affiche la vue pour répondre à un questionnaire.
     * Récupère le questionnaire et ses questions triées par position.
     *
     * @param int|null $id ID du questionnaire (optionnel, sinon depuis GET).
     */
    public function repondre($id = null) {
        if ($id === null) {
            $id = isset($_GET['id']) ? $_GET['id'] : null;
        }

        if (empty($id)) {
            echo "<script>window.location.href = './?c=erreur&a=404';</script>";
            return;
        }

        $questionnaire = $this->questionnaireModel->getQuestionnaire($id);

        if (!$questionnaire) {
            echo "<script>window.location.href = './?c=erreur&a=404';</script>";
            return;
        }

        $json_regles = $questionnaire['groupes_autorises'] ?? '';
        if (!$this->aLeDroitDAcces($json_regles, $_SESSION['cas_groupes']) && $questionnaire['id_createur'] != $_SESSION['cas_user']) {
            echo "<script>window.location.href = './?c=erreur&a=droits';</script>";
            exit();
        }

        if ($questionnaire['id_createur'] != $_SESSION['cas_user']) {
            
            $aDejaRepondu = $this->reponses_utilisateurModel->aDejaRepondu($id, $_SESSION['cas_user']);

            if ($aDejaRepondu) {
                echo "<script>window.location.href = './?c=erreur&a=deja-repondu';</script>";
                exit();
            }
        }

        $questions = $this->questionModel->getQuestionPar(['id_questionnaire' => $id]);
        $createur = null;
        if (isset($questionnaire['id_createur'])) {
            $createur = $this->questionnaireModel->getUtilisateurParId($questionnaire['id_createur']);
        }
        if ($questionnaire['brouillon'] == 0) {
            echo 'Ce questionnaire est encore en brouillon, vous ne pouvez pas y répondre.';
            return;
        } else {

            $questions = $this->questionModel->getQuestionPar(['id_questionnaire' => $id]);
            $createur = null;
            if (isset($questionnaire['id_createur'])) {
                $createur = $this->questionnaireModel->getUtilisateurParId($questionnaire['id_createur']);
            }

            // trier par position si disponible
            usort($questions, function($a, $b) {
                $pa = isset($a['position']) ? (int)$a['position'] : 0;
                $pb = isset($b['position']) ? (int)$b['position'] : 0;
                return $pa <=> $pb;
            });

            require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'repondreQuestionnaire.php');
        }
    }

    /**
     * Affiche la vue de création d'un nouveau questionnaire.
     */
    public function ajouterQuestionnaire($id = null) {
        if ($id !== null) {
            $questionnaire = $this->questionnaireModel->getQuestionnaire($id);
            if (!$questionnaire) {
                echo "<script>window.location.href = './?c=erreur&a=404';</script>";
                return;
            }
            if ($questionnaire['brouillon'] == 1) {
                echo "<script>window.location.href = './?c=erreur&a=404';</script>";
                return;
            }

            if (!isset($_SESSION['cas_user']) || $questionnaire['id_createur'] !== $_SESSION['cas_user']) {
                echo "<script>window.location.href = './?c=erreur&a=droits';</script>";
                exit();
            }

            $questionnaire= $this->getQuestionnaireComplet($id);
        } else {
            $questionnaire = null;
        }
        require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'creationQuestionnaire.php');
    }

    /**
     * Liste les questionnaires selon l'utilisateur connecté ou tous si non connecté.
     */
    public function listerQuestionnaires() {
        $this->autoSuppression();

        $tous_les_questionnairesPublier = $this->questionnaireModel->getQuestionnairePar(["brouillon" => 1]);
        $questionnaires_visibles = [];

        foreach ($tous_les_questionnairesPublier as $index => $questionnaire) {
            $json_regles = $questionnaire['groupes_autorises'] ?? '';

            if ($this->aLeDroitDAcces($json_regles, $_SESSION['cas_groupes'])) {

                if (!isset($questionnaire['id_createur'])) {
                    $createur = $this->questionnaireModel->getUtilisateurParId($questionnaire['id_createur']);
                    $questionnaire['createur_nom'] = $createur['nom'] ?? '';
                    $questionnaire['createur_prenom'] = $createur['prenom'] ?? '';
                } else {
                    $questionnaire['createur_nom'] = '';
                    $questionnaire['createur_prenom'] = '';
                }

                $questionnaires_visibles[] = $questionnaire;
            }
        }
        $questionnaires = $questionnaires_visibles;

        require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'listerQuestionnaire.php');
    }


    public function accederParCode() {
        $code = $_POST['code'] ?? null;

        if (empty($code)) {
            echo "<script>window.location.href = './?c=erreur&a=404';</script>";
            return;
        }

        $questionnaire = $this->questionnaireModel->getQuestionnaireParCode($code);

        if (!$questionnaire) {
            echo "<script>window.location.href = './?c=erreur&a=404';</script>";
            return;
        }

        $json_regles = $questionnaire['groupes_autorises'] ?? '';
        if (!$this->aLeDroitDAcces($json_regles, $_SESSION['cas_groupes']) && $questionnaire['id_createur'] != $_SESSION['cas_user']) {
            echo "<script>window.location.href = './?c=erreur&a=droits';</script>";
            exit();
        }

        $url_redirection = "./?c=questionnaire&a=repondre&id=" . $questionnaire['id'];
        echo "<script>window.location.href = '" . $url_redirection . "';</script>";
        exit();
    }


    /**
     * Vérifie si l'utilisateur peut voir les résultats du questionnaire (doit être le créateur).
     */
    public function peutVoirResultatsQuestionnaire() {
        $id_questionnaire = isset($_GET['id_questionnaire']) ? $_GET['id_questionnaire'] : null;
        
        if (!is_null($id_questionnaire)) {
            if ($_SESSION['id'] != $this->questionnaireModel->getQuestionnaire($id_questionnaire)['id_createur']) {
                echo "<script>window.location.href = './?c=erreur&a=droits';</script>";
                return;
            } else {
                $resultats = $this->reponses_utilisateurModel->getReponse($id_questionnaire, $_SESSION['id_utilisateur']);
                // require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'traitementResultats.php');
            }
            
        } else {
            echo "<script>window.location.href = './?c=erreur&a=404';</script>";
            return;
        }
    }

    /**
     * Enregistre ou met à jour un questionnaire avec les données POST.
     *
     * @return bool True si l'enregistrement réussit, false sinon.
     */
    public function enregistrerQuestionnaire() {
        $id = $_POST['id_questionnaire'] ?? $_POST['id'] ?? null;
        $titre = isset($_POST['nom-questionnaire']) ? $_POST['nom-questionnaire'] : null;
        $date_expiration = isset($_POST['date-expriration']) ? $_POST['date-expriration'] : null;
        $brouillon = isset($_POST['mode_enregistrement']) ? ($_POST['mode_enregistrement'] == "brouillon" ? 0 : 1) : 1;
        $id_createur = $_SESSION['cas_user'] ?? session_id();
        $code = isset($_POST['code']) ? $_POST['code'] : null;

        if (trim((string)$id) === "") {
            $id = null;
        }

        $mon_profil = analyserProfilUtilisateur($_SESSION['cas_groupes']);
        $cibles_selectionnees = $_POST['groupes_cibles'] ?? [];
        $site_du_questionnaire = $mon_profil['sites'][0] ?? 'limoges'; 
        
        $regles_acces = [
            "site_requis" => $site_du_questionnaire,
            "groupes_requis" => $cibles_selectionnees
        ];
        $json_pour_bdd = json_encode($regles_acces);

        if (!is_null($id)) {
            $ajoutOk = $this->questionnaireModel->modifier($id, $titre, $date_expiration, $json_pour_bdd, $brouillon);
            $idQuestionnaire = $id;
        } else {
            $ajoutOk = $this->questionnaireModel->creerQuestionnaire($titre, $id_createur, $date_expiration, $code, $json_pour_bdd, $brouillon);
            $idQuestionnaire = $this->questionnaireModel->getIdDerniereInsertion();
        }

        if ($ajoutOk && isset($_POST['liste-questions'])) {
            $questionsJson = json_decode($_POST['liste-questions'], true);
            
            if (is_array($questionsJson)) {
                if (!is_null($id)) {
                    $this->questionModel->supprimerQuestionsParQuestionnaire($idQuestionnaire);
                }

                foreach ($questionsJson as $q) {
                    $intitule = $q['intitule'] ?? '';
                    $type = $q['type'] ?? 'textfield';
                    $position = $q['position'] ?? 0;
                    $est_obligatoire = ($q['est_obligatoire'] === "true" || $q['est_obligatoire'] === true || $q['est_obligatoire'] == 1) ? 1 : 0;
                    
                    $this->questionModel->creerQuestion($idQuestionnaire, $intitule, $type, $est_obligatoire, $position);
                    $idQuestion = $this->questionModel->getIdDerniereInsertion(); 

                    if (isset($q['choix']) && is_array($q['choix'])) {
                        foreach ($q['choix'] as $choixTexte) {
                            $this->choix_possibleModel->creerChoix($idQuestion, $choixTexte);
                        }
                    }
                }
            }
        }

        if ($ajoutOk) {
            header("Location: ./?c=home");
            exit();
        } else {
            echo 'Erreur lors de l\'enregistrement.';
        }

        return $ajoutOk;
    }

    /**
     * Supprime un questionnaire de la base de données si il existe.
     *
     * @param int|null $id ID du questionnaire à supprimer (optionnel, sinon depuis GET).
     * @return bool True si la suppression réussit, false sinon.
     */
    public function supprimerParId($id = null) {
        if ($id === null) {
            $id = isset($_GET['id']) ? $_GET['id'] : null;
        }

        if (empty($id)) {
            return false;
        }

        $questionnaire = $this->questionnaireModel->getQuestionnaire($id);

         if ($questionnaire['id_createur'] != $_SESSION['cas_user']) {
            echo "<script>window.location.href = './?c=erreur&a=droits';</script>";
            exit();
        }

        if (isset($questionnaire)) {
            $supprOK = $this->questionnaireModel->supprimer($id);  
        }

        return $supprOK;
    }

    /**
     * Supprime un questionnaire de la base de données si il existe.
     *
     * @param int|null $id ID du questionnaire à supprimer (optionnel, sinon depuis GET).
     * @return bool True si la suppression réussit, false sinon.
     */
    public function supprimer() {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo "<script>window.location.href = './?c=erreur&a=404';</script>";
            exit();
        }

        if (empty($id)) {
            echo "<script>window.location.href = './?c=erreur&a=404';</script>";
            exit();
        }

        $questionnaire = $this->questionnaireModel->getQuestionnaire($id);

        if (!isset($_SESSION['cas_user']) || $questionnaire['id_createur'] !== $_SESSION['cas_user']) {
            echo "<script>window.location.href = './?c=erreur&a=droits';</script>";
            exit();
        }

        $suppressionOk = $this->questionnaireModel->supprimer($id); 

        if ($suppressionOk) {
            header("Location: ./?c=questionnaire&a=resultats");
            exit();
        } else {
            die("Une erreur est survenue lors de la suppression en base de données.");
        }
    }

    /**
     * Retourne le dernier ID inséré dans la base de données pour les questionnaires.
     *
     * @return int Dernier ID inséré.
     */
    public function getIdDerniereInsertion() {
        return $this->questionnaireModel->getIdDerniereInsertion();
    }

    /**
     * Exporte les résultats d'un questionnaire au format CSV.
     * Génère un fichier téléchargeable avec les réponses.
     */
    public function exporterEnCSV() {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo "<script>window.location.href = './?c=erreur&a=404';</script>";
            return;
        }

        $questionnaire = $this->questionnaireModel->getQuestionnaire($id);
        if (!$questionnaire) {
            http_response_code(404);
            echo "<script>window.location.href = './?c=erreur&a=404';</script>";
            return;
        }

        // Vérification des droits
        if ($questionnaire['id_createur'] != $_SESSION['cas_user']) {
            echo "<script>window.location.href = './?c=erreur&a=droits';</script>";
            exit();
        }

        $titre = $questionnaire['titre'];
        $resultats = $this->reponses_utilisateurModel->getReponsePourCSV($id);

        if (empty($resultats)) {
            http_response_code(204);
            exit;
        }

        $filename = "export_" . $titre . ".csv";

        header("Content-Type: text/csv; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        $output = fopen("php://output", "w");

        fwrite($output, "\xEF\xBB\xBF"); // s'occupe des caractères spéciaux pour excel.

        // En-têtes imposés
        fputcsv($output, ["Questions", "répondants", "choix", "réponses"], ';');

        foreach ($resultats as $row) {
            fputcsv($output, [
                $row['question'],
                $row['repondant'],
                $row['choix'],
                $row['reponse_libre']
            ], ';');
        }

        fclose($output);
        exit;
    }

    /**
     * affichage temporaire en attendant son implementation
     *
     * @param int|null $id ID du questionnaire à afficher (optionnel, sinon depuis GET).
     */
    public function detailQuestionnaire($id) {
        if (empty($id)) {
            echo "<script>window.location.href = './?c=erreur&a=404';</script>";
            return;
        }
        
        if (!$this->questionnaireModel->existant($id)) {
            echo "<script>window.location.href = './?c=erreur&a=404';</script>";
            return;
        }

        $total_reponses = $this->questionnaireModel->getNombreRepondants($id);
        $total_questions = $this->questionnaireModel->getNombreQuestions($id);

        $questionnaire = $this->questionnaireModel->getQuestionnaire($id);
        if($questionnaire['id_createur'] == $_SESSION['cas_user']){
            if ($questionnaire['brouillon'] == 1) {
                $repondants = $this->reponses_utilisateurModel->getRepondantsParQuestionnaire($id);
                require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'detailQuestionnaire.php');
            } else
            if ($questionnaire['brouillon'] == 0) {
                header('Location: ?c=questionnaire&a=creation&id=' . $id);
                exit;            
            } else {
                echo "erreur : format de données du questionnaire invalide -> le Brouillon n'est pas à 1 ni 0.";
            }
        } else {
            echo "<script>window.location.href = './?c=erreur&a=droits';</script>";
            exit();
        }
    }

    /**
     * Modifie le titre d'un questionnaire existant avec les données POST.
     * Affiche un message de confirmation ou d'erreur.
     */
    public function changerTitre() {
        $id = $_POST["id"];
        $titre = $_POST["titre"];
        echo("id = " . $id . "titre = " . $titre);

        $date_expiration = $this->questionnaireModel->getQuestionnaire($id)['date_expiration'];
        $groupes_autorises = $this->questionnaireModel->getQuestionnaire($id)['groupes_autorises'];

        $questionnaire = new Questionnaire();
        $questionnaire-> modifier($id, $titre, $date_expiration, $groupes_autorises);

        echo "OK";
    }

    /**
     * Affiche le formulaire de modification d'un questionnaire.
     */
    public function modifier() {
    $id = $_GET['id'] ?? null;
    if (!$id) { echo "<script>window.location.href = './?c=erreur&a=404';</script>"; return; }

    $questionnaire = $this->questionnaireModel->getQuestionnaire($id);
    if (!$questionnaire) { echo "<script>window.location.href = './?c=erreur&a=404';</script>"; return; }

    if ($questionnaire['id_createur'] != $_SESSION['cas_user']) {
        echo "<script>window.location.href = './?c=erreur&a=droits';</script>";
        exit();
    }

    $regles = json_decode($questionnaire['groupes_autorises'], true);
    $groupes_actuels = $regles['groupes_requis'] ?? [];

    require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'modifierQuestionnaire.php');
}

    /**
     * Vérifie si un utilisateur a le droit de voir/répondre à un questionnaire
     * @param string $json_regles Le contenu de la colonne 'groupes_autorises'
     * @param array $mes_groupes Le tableau des groupes propres de l'utilisateur
     * @return bool True si autorisé, False si bloqué
     */
    public function aLeDroitDAcces($json_regles, $mes_groupes) {
        // Si vide alors droits publics
        if (empty($json_regles)) {
            return true; 
        }

        $regles = json_decode($json_regles, true);
        
        if (!is_array($regles)) {
            return false;
        }

        $site_requis = $regles['site_requis'] ?? '';
        $groupes_requis = $regles['groupes_requis'] ?? [];

        // vérification du site
        if ($site_requis !== '') {
            $a_le_bon_site = false;
            $groupes_site = [
                "iut-etudiants-$site_requis", 
                "iut-personnel-$site_requis",
                "iut-enseignants-$site_requis"
            ];

            foreach ($groupes_site as $g) {
                if (in_array($g, $mes_groupes)) {
                    $a_le_bon_site = true;
                    break;
                }
            }
            
            if (!$a_le_bon_site) {
                return false;
            }
        }

        // vérification du groupe/formation
        if (!empty($groupes_requis)) {
            $intersection = array_intersect($mes_groupes, $groupes_requis);
            if (count($intersection) == 0) {
                return false; // bon site, mais pas la bonne formation
            }
        }

        return true;
    }



    /**
     * Affiche la vue d'analyse graphique des résultats d'un questionnaire.
     *
     * @param int|null $id ID du questionnaire à analyser (optionnel, sinon depuis GET).
     */
    public function analyseGraphique($id = null) {
        if ($id === null) {
            $id = isset($_GET['id']) ? $_GET['id'] : null;
        }

        if (empty($id)) {
            echo "<script>window.location.href = './?c=erreur&a=404';</script>";
            return;
        }

        $questionnaire = $this->questionnaireModel->getQuestionnaire($id);

        if (!$questionnaire) {
            echo "<script>window.location.href = './?c=erreur&a=404';</script>";
            return;
        }

        if ($questionnaire['id_createur'] != $_SESSION['cas_user']) {
            echo "<script>window.location.href = './?c=erreur&a=droits';</script>";
        exit();
    }

        $resultats_fermes = $this->statistiqueModel->getStatsQuestionsFermees($id);
        $statistiques_formatees = [];
        $palette_couleurs = ['#3273dc', '#48c774', '#ffdd57', '#f14668', '#b86bff', '#00d1b2'];

        foreach ($resultats_fermes as $ligne) {
            $id_q = $ligne['id_question'];
            if (!isset($statistiques_formatees[$id_q])) {
                $statistiques_formatees[$id_q] = [
                    'id_question' => $id_q,
                    'titre_question' => $ligne['titre_question'],
                    // Radio = Pie, Checkbox = Bar
                    'type_graphique' => ($ligne['type_question'] == 'radio') ? 'pie' : 'bar',
                    'labels' => [],
                    'donnees' => [],
                    'couleurs' => []
                ];
            }
            $statistiques_formatees[$id_q]['labels'][] = $ligne['label'];
            $statistiques_formatees[$id_q]['donnees'][] = (int) $ligne['nb_votes'];
            
            $index_couleur = count($statistiques_formatees[$id_q]['couleurs']) % count($palette_couleurs);
            $statistiques_formatees[$id_q]['couleurs'][] = $palette_couleurs[$index_couleur];
        }
        
        $json_statistiques = json_encode(array_values($statistiques_formatees));

        $questions_ouvertes = $this->statistiqueModel->getStatsQuestionsOuvertes($id);

        require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'analyseGraphique.php');
    }



    public function getQuestionnaireComplet($id) {
        $questionnaire = $this->questionnaireModel->getQuestionnaire($id);
        if (!$questionnaire) return null;

        $questions = $this->questionModel->getQuestionPar(["id_questionnaire" => $id]);

        foreach ($questions as &$q) {
            $q['choix'] = $this->choix_possibleModel->getChoixDeQuestion($q['id']);
        }

        $questionnaire['questions'] = $questions;

        return $questionnaire;
    }

    public function autoSuppression() {
        $listeQuestionnaires = $this->questionnaireModel->getTousLesQuestionnaires();
        $dateActuelle = new \DateTime();

        foreach ($listeQuestionnaires as $qtnaire) {
            $dateExpiration = new \DateTime($qtnaire['date_expiration']);
            $interval = $dateActuelle->diff($dateExpiration);
            $jours = (int)$interval->format('%r%a');
            if ($jours <= -1097) {   // date expirée depuis 3 ans ou plus
                $this->questionnaireModel->supprimer($qtnaire['id']);
            }
        }
    }

    /**
     * Affiche la vue des résultats du questionnaire.
     */
    public function resultatsQuestionnaire() {
        $this->autoSuppression();

        $questionnairesFinis = $this->questionnaireModel->getQuestionnairePar(["id_createur" => $_SESSION['cas_user'] , "brouillon" => 1]);
        $questionnairesBrouillons = $this->questionnaireModel->getQuestionnairePar(["id_createur" => $_SESSION['cas_user'] , "brouillon" => 0]);

        require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'resultatsQuestionnaire.php');
    }

    /**
     * Duplique un questionnaire existant en créant une copie avec un nouveau titre.
     * Le nouveau titre est basé sur l'original avec " (copie)" ajouté à la fin.
     */
    public function dupliquer($id = null) {
    if (empty($id)) {
        echo "<script>window.location.href = './?c=erreur&a=404';</script>";
        exit();
    }

    $original = $this->questionnaireModel->getQuestionnaire($id);
    if (!$original) {
        echo "<script>window.location.href = './?c=erreur&a=404';</script>";
        exit();
    }

    if (!isset($_SESSION['cas_user']) || $original['id_createur'] !== $_SESSION['cas_user']) {
        echo "<script>window.location.href = './?c=erreur&a=droits';</script>";
        exit();
    }

    $nouveauTitre = $original['titre'] . ' (copie)';
    $succes = $this->questionnaireModel->dupliquerQuestionnaireComplet($id, $nouveauTitre, 0);

    if ($succes) {
        header("Location: ./?c=questionnaire&a=resultats");
        exit();
    } else {
        die("Une erreur est survenue lors de la duplication.");
    }
}
}