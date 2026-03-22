<?php

namespace App\Controllers;

use App\Models\Questionnaire;
use App\Models\Question;
use App\Models\Reponses_utilisateur;
use App\Models\Choix_possible;
use App\Controllers\QuestionController;

require_once 'config.php';


class QuestionnaireController {

    private $questionnaireModel;
    private $questionModel;
    private $reponses_utilisateurModel;
    private $choix_possibleModel;
    private $questionController;

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
        $choix_possibleModel = new Choix_possible();
        $this->choix_possibleModel = $choix_possibleModel;
        $questionController = new QuestionController();
        $this->questionController = $questionController;
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
                echo 'Questionnaire introuvable.';
                return;
            }
            if ($questionnaire['brouillon'] == 1) {
                echo 'Les questionnaires déjà publiés ne peuvent pas être modifiés.       :)';
                return;
            }
            if ($questionnaire['id_createur'] != $_SESSION['cas_user']) {
                echo 'Vous n\'avez pas accès à ce questionnaire.';
                return;
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

    /**
     * Vérifie si l'utilisateur peut voir les résultats du questionnaire (doit être le créateur).
     */
    public function peutVoirResultatsQuestionnaire() {
        $id_questionnaire = isset($_GET['id_questionnaire']) ? $_GET['id_questionnaire'] : null;
        
        if (!is_null($id_questionnaire)) {
            if ($_SESSION['cas_user'] != $this->questionnaireModel->getQuestionnaire($id_questionnaire)['id_createur']) {
                echo "erreur : vous n'êtes pas le créateur de ce questionnaire.";
                return;
            } else {
                $resultats = $this->reponses_utilisateurModel->getReponse($id_questionnaire, $_SESSION['cas_user']);
                // require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'traitementResultats.php');
            }
            
        } else {
            echo 'erreur : id du questionnaire introuvable.';
            return;
        }
    }

    /**
     * WIP -> à découper mtn que c'est corriger
     */
    public function enregistrerQuestionnaire() {

        // -----------------------------
        // Récupération des données POST
        // -----------------------------
        $mode = $_POST['mode_enregistrement'] ?? null;   // 'publier' ou 'brouillon'
        $id_questionnaire = $_POST['id_questionnaire'] ?? null; // vide si nouveau

        $liste_questions_json = $_POST['liste-questions'] ?? '[]';
        $liste_questions = json_decode($liste_questions_json, true);

        $titre = $_POST['nom-questionnaire'] ?? null;
        $date_expiration = $_POST['date-expiration'] ?? null;

        // Construction du JSON groupes_autorises
        $groupes_autorises = json_encode([
            "site_requis" => "limoges", // temporaire
            "groupes_requis" => $_POST["groupes_autorises"] ?? []
        ], JSON_UNESCAPED_UNICODE);

        // Détermination du statut brouillon / publié
        $est_brouillon = ($mode === 'publier') ? 1 : 0;

        // -----------------------------
        // CAS 1 : Création d’un nouveau questionnaire
        // -----------------------------
        if ($id_questionnaire === null || $id_questionnaire === '') {

            // Création du questionnaire (brouillon ou publié directement)
            $nouvel_id = $this->questionnaireModel->creerQuestionnaire(
                $titre,
                $_SESSION['cas_user'],
                $date_expiration,
                null, // code (géré dans le modèle)
                $groupes_autorises,
                $est_brouillon
            );

            // Enregistrement des questions
            $_POST['liste-questions'] = json_encode($liste_questions);
            $this->questionController->enregistrerQuestions($nouvel_id);

            header("Location: ?c=home");
            exit;
        }

        // -----------------------------
        // CAS 2 : Modification d’un questionnaire existant
        // -----------------------------

        // Récupération du questionnaire existant
        $questionnaire = $this->questionnaireModel->getQuestionnaire($id_questionnaire);

        // Sécurité : un questionnaire publié n’est jamais modifiable
        if ($questionnaire['brouillon'] == 1) {
            header("Location: ?c=home");
            exit;
        }

        // -----------------------------
        // CAS 2A : Modification d’un brouillon (sans publication)
        // -----------------------------
        if ($mode === 'brouillon') {

            // Mise à jour du brouillon
            $this->questionnaireModel->modifier(
                $id_questionnaire,
                $titre,
                $date_expiration,
                $groupes_autorises
            );

            // Mise à jour des questions du brouillon
            $this->questionController->mettreAJourQuestions($id_questionnaire, $liste_questions);

            header("Location: ?c=home");
            exit;
        }

        // -----------------------------
        // CAS 2B : Publication d’un brouillon
        // -----------------------------
        if ($mode === 'publier') {

            // 1) Mettre à jour le brouillon AVANT de figer l’état
            $this->questionnaireModel->modifier(
                $id_questionnaire,
                $titre,
                $date_expiration,
                $groupes_autorises
            );

            $this->questionController->mettreAJourQuestions($id_questionnaire, $liste_questions);

            // 2) Créer un questionnaire publié (snapshot figé)
            $id_publie = $this->questionnaireModel->creerQuestionnaire(
                $titre,
                $_SESSION['cas_user'],
                $date_expiration,
                null, // code généré automatiquement
                $groupes_autorises,
                1 // publié
            );

            // 3) Recréer toutes les questions dans le questionnaire publié
            $_POST['liste-questions'] = json_encode($liste_questions);
            $this->questionController->enregistrerQuestions($id_publie);

            header("Location: ?c=home");
            exit;
        }

        // Sécurité fallback
        header("Location: ?c=home");
        exit;
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

    /**
     * affiichage temporaire en attendant son implementation
     *
     * @param int|null $id ID du questionnaire à afficher (optionnel, sinon depuis GET).
     */
    public function detailQuestionnaire($id) {
        if (empty($id)) {
            echo 'Identifiant de questionnaire manquant.';
            return;
        }
        
        if (!$this->questionnaireModel->existant($id)) {
            echo 'Questionnaire introuvable.';
            return;
        }

        $questionnaire = $this->questionnaireModel->getQuestionnaire($id);
        if($questionnaire['id_createur'] == $_SESSION['cas_user']){
            if ($questionnaire['brouillon'] == 1) {
                require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'detailQuestionnaire.php');
            }
            if ($questionnaire['brouillon'] == 0) {
                header('Location: ?c=questionnaire&a=creation&id=' . $id);
                exit;            
            }
            echo "erreur : format de données du questionnaire invalide -> le Brouillon n'est pas à 1 ni 0.";
        } else {
            echo 'Vous n\'avez pas accès à ce questionnaire.';
            return;
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

        $questionnaire = new Questionnaire();
        $questionnaire-> modifier($id, $titre, $date_expiration, '');

        echo "OK";
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
}

    