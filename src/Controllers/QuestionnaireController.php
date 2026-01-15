<?php

namespace App\Controllers;

use App\Models\Questionnaire;
use App\Models\Question;
use App\Models\Reponses_utilisateur;

class QuestionnaireController {

    private $questionnaireModel;
    private $questionModel;
    private $reponses_utilisateurModel;

    public function __construct() {
        $questionnaireModel = new Questionnaire();
        $this->questionnaireModel = $questionnaireModel;
        $questionModel = new Question();
        $this->questionModel = $questionModel;
        $reponses_utilisateurModel = new Reponses_utilisateur();
        $this->reponses_utilisateurModel = $reponses_utilisateurModel;
    }

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

        $questions = $this->questionModel->getQuestionBy(['id_questionnaire' => $id]);

        // trier par position si disponible
        usort($questions, function($a, $b) {
            $pa = isset($a['position']) ? (int)$a['position'] : 0;
            $pb = isset($b['position']) ? (int)$b['position'] : 0;
            return $pa <=> $pb;
        });

        require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'repondreQuestionnaire.php');
    }

    public function ajouterQuestionnaire() {
        require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'creationQuestionnaire.php');
    }

    public function listerQuestionnaires() {
        if (!isset($_SESSION['id_utilisateur'])) {
            $questionnaires = $this->questionnaireModel->getAllQuestionnaires();
        } else {
            $questionnaires = $this->questionnaireModel->getQuestionnairesByUserId($_SESSION['id_utilisateur']);
        }
        require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'listerQuestionnaire.php');
    }

    public function peutVoirResultatsQuestionnaire() {
        $id_questionnaire = isset($_GET['id_questionnaire']) ? $_GET['id_questionnaire'] : null;
        
        if (!is_null($id_questionnaire)) {
            if ($_SESSION['id'] != $this->questionnaireModel->getQuestionnaire($id_questionnaire)['id_createur']) {
                echo 'error : you are not the creator of this questionnaire.';
                return;
            } else {
                $resultats = $this->reponses_utilisateurModel->getReponse($id_questionnaire, $_SESSION['id_utilisateur']);
                // require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'traitementResultats.php');
            }
            
        } else {
            echo 'error : unable to find the questionnaire id.';
            return;
        }
    }

    public function enregistrerQuestionnaire() {
        $id = isset($_POST['id']) ? $_POST['id'] : null;
        $titre = isset($_POST['nom-questionnaire']) ? $_POST['nom-questionnaire'] : null;
        $date_expiration = isset($_POST['date-expriration']) ? $_POST['date-expriration'] : null;
        $id_createur = isset($_POST['id_createur']) ? $_POST['id_createur'] : null;
        $code = isset($_POST['code']) ? $_POST['code'] : null;

        if (isset($id)) {
            $ajoutOk = $this->questionnaireModel->update($id, $titre, $date_expiration);
        } else {
            //for ($i = 0; $i < 500; $i++) { //pour les testes de gestion de conflit de code
            $ajoutOk = $this->questionnaireModel->createQuestionnaire($titre, $id_createur, $date_expiration, $code);
        }

        if ($ajoutOk) {
            require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'home.php');
        } else {
            echo 'Erreur lors de l\'enregistrement.';
        }

        return $ajoutOk;
    }

    public function supprimer($id = null) {
        if ($id === null) {
            $id = isset($_GET['id']) ? $_GET['id'] : null;
        }

        if (empty($id)) {
            return false;
        }

        $questionnaire = $this->questionnaireModel->getQuestionnaire($id);

        if ($questionnaire) {
            if($this->questionnaireModel->delete($id)){
                $questions = $this->questionModel->getQuestionBy(['id_questionnaire' => $id]);
                foreach ($questions as $question) {
                    $this->questionModel->delete($question['id']);
                }
            }
        }

        return false;
    }

    public function lastInsertId() {
        return $this->questionnaireModel->lastInsertId();
    }
}

    