<?php

require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Models'.DIRECTORY_SEPARATOR.'Questionnaire.php');

class QuestionnaireController {

    private $questionnaireModel;

    public function __construct() {
        $questionnaireModel = new Questionnaire();
        $this->questionnaireModel = $questionnaireModel;
    }


    public function ajouterQuestionnaire() {
        require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'creationQuestionnaire.php');
    }

    public function listerQuestionnaires() {
        $questionnaires = $this->questionnaireModel->getAllQuestionnaires();

        require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'listerQuestionnaire.php');
    }

    public function resultatsQuestionnaire() {
        require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'resultatsQuestionnaire.php');
    }

    public function enregistrer() {
        $id = isset($_POST['id']) ? $_POST['id'] : null;
        $titre = isset($_POST['titre']) ? $_POST['titre'] : null;
        $date_expiration = isset($_POST['date_expiration']) ? $_POST['date_expiration'] : null;
        $id_createur = isset($_POST['id_createur']) ? $_POST['id_createur'] : null;
        $code = isset($_POST['code']) ? $_POST['code'] : null;

        if (isset($id)) {
            $ajoutOk = $this->questionnaireModel->update($id, $titre, $date_expiration);
        } else {
            $ajoutOk = $this->questionnaireModel->createQuestionnaire($titre, $id_createur, $date_expiration, $code);
        }

        if ($ajoutOk) {
            require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'home.php');
        } else {
            echo 'Erreur lors de l\'enregistrement.';
        }
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
            return $this->questionnaireModel->delete($id);
        }

        return false;
    }

}

    