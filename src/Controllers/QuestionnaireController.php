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
        require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'listerQuestionnaires.php');
    }

    public function resultatsQuestionnaire($id) {
        $questionnaire = $this->questionnaireModel->getQuestionnaire($id);
        require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'resultat_questionnaire.php');
    }

    public function supprimer($id) {
        $id = $_GET['id'];

        $questionnaire = $this->questionnaireModel->getQuestionnaire($id);

        if (count($questionnaire) > 0) {
            $this->questionnaireModel->delete($id);
        }
    }





}

    