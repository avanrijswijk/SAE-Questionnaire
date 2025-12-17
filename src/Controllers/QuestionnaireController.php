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

    