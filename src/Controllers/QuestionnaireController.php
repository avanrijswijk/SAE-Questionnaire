<?php

namespace App\Controllers;

use App\Models\Questionnaire;
use App\Models\Question;
use App\Models\Reponses_utilisateur;

class QuestionnaireController {

    private $questionnaireModel;
    private $questionModel;
    private $reponses_utilisateurModel;

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
            echo 'Identifiant de questionnaire manquant.';
            return;
        }

        $questionnaire = $this->questionnaireModel->getQuestionnaire($id);

        if (!$questionnaire) {
            echo 'Questionnaire introuvable.';
            return;
        }

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

    /**
     * Affiche la vue de création d'un nouveau questionnaire.
     */
    public function ajouterQuestionnaire() {
        require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'creationQuestionnaire.php');
    }

    /**
     * Liste les questionnaires selon l'utilisateur connecté ou tous si non connecté.
     */
    public function listerQuestionnaires() {
        if (!isset($_SESSION['id_utilisateur'])) {
            $questionnaires = $this->questionnaireModel->getTousLesQuestionnaires();
        } else {
            $questionnaires = $this->questionnaireModel->getQuestionnairesParIdUtilisateur($_SESSION['id_utilisateur']);
        }

        foreach ($questionnaires as $index => $questionnaire) {
            if (!isset($questionnaire['id_createur'])) {
                continue;
            }
            $createur = $this->questionnaireModel->getUtilisateurParId($questionnaire['id_createur']);
            $questionnaires[$index]['createur_nom'] = $createur['nom'] ?? '';
            $questionnaires[$index]['createur_prenom'] = $createur['prenom'] ?? '';
        }
        require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'listerQuestionnaire.php');
    }

    /**
     * Vérifie si l'utilisateur peut voir les résultats du questionnaire (doit être le créateur).
     */
    public function peutVoirResultatsQuestionnaire() {
        $id_questionnaire = isset($_GET['id_questionnaire']) ? $_GET['id_questionnaire'] : null;
        
        if (!is_null($id_questionnaire)) {
            if ($_SESSION['id'] != $this->questionnaireModel->getQuestionnaire($id_questionnaire)['id_createur']) {
                echo "erreur : vous n'êtes pas le créateur de ce questionnaire.";
                return;
            } else {
                $resultats = $this->reponses_utilisateurModel->getReponse($id_questionnaire, $_SESSION['id_utilisateur']);
                // require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'traitementResultats.php');
            }
            
        } else {
            echo 'erreur : id du questionnaire introuvable.';
            return;
        }
    }

    /**
     * Enregistre ou met à jour un questionnaire avec les données POST.
     *
     * @return bool True si l'enregistrement réussit, false sinon.
     */
    public function enregistrerQuestionnaire() {
        $id = isset($_POST['id']) ? $_POST['id'] : null;
        $titre = isset($_POST['nom-questionnaire']) ? $_POST['nom-questionnaire'] : null;
        $date_expiration = isset($_POST['date-expriration']) ? $_POST['date-expriration'] : null;
        $id_createur = $_SESSION['cas_user']
            ?? session_id();
        $code = isset($_POST['code']) ? $_POST['code'] : null;

        // Gestion des règles d'accès
        $mon_profil = analyserProfilUtilisateur($_SESSION['cas_groupes']);
        $cibles_selectionnees = $_POST['groupes_cibles'] ?? [];
        // On détermine le site du créateur du questionnaire
        $site_du_questionnaire = $mon_profil['sites'][0] ?? 'limoges'; 
        // On crée nos règles d'accès en fonction du site et des groupes cibles sélectionnés
        $regles_acces = [
            "site_requis" => $site_du_questionnaire,
            "groupes_requis" => $cibles_selectionnees
        ];
        // On encode ces règles en JSON pour la base de données
        $json_pour_bdd = json_encode($regles_acces);

        if (isset($id)) {
            $ajoutOk = $this->questionnaireModel->modifier($id, $titre, $date_expiration, $json_pour_bdd);
        } else {
            //for ($i = 0; $i < 500; $i++) { //pour les testes de gestion de conflit de code
            $ajoutOk = $this->questionnaireModel->creerQuestionnaire($titre, $id_createur, $date_expiration, $code, $json_pour_bdd);
        }

        if ($ajoutOk) {
            require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'home.php');
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
            exit("ID manquant.");
        }

        if (empty($id)) {
            return false;
        }

        $questionnaire = $this->questionnaireModel->getQuestionnaire($id);

        if (isset($questionnaire)) {
            $supprOK = $this->questionnaireModel->supprimer($id);  
        }

        require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'resultatsQuestionnaire.php');
        return $supprOK;
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
            exit("ID manquant.");
        }

        $titre = $this->questionnaireModel->getTitreAvecTireParID($id);
        if (!$titre) {
            http_response_code(404);
            exit("Questionnaire introuvable.");
        }

        $resultats = $this->reponses_utilisateurModel->getReponsePourCSV($id);
        if (empty($resultats)) {
            http_response_code(204); // pas de contenu
            exit;
        }
        

        $filename = "export_" . $titre . ".csv";

        header("Content-Type: text/csv; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        $output = fopen("php://output", "w");
        fputcsv($output, array_keys($resultats[0]), ';');

        foreach ($resultats as $row) {
            fputcsv($output, $row, ';');
        }

        fclose($output);
        exit;
    }
}

    