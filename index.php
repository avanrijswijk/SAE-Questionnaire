<?php

//session_start();

require 'vendor/autoload.php';
require_once(__DIR__.DIRECTORY_SEPARATOR.'bootstrap.php');


use App\Controllers\QuestionnaireController;
use App\Controllers\AcceptesController;


// ajout de l'en tête
require_once(__DIR__.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'header.php');

// routage simple (normaliser en minuscules)
$controller = isset($_GET['c'])? strtolower($_GET['c']) : 'connexion';
$action = isset($_GET['a']) ? strtolower($_GET['a']) : 'lister';


    switch ($controller) {

        case 'home':
            require_once(__DIR__.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'Controllers'.DIRECTORY_SEPARATOR.'homeController.php');
            break;

        case 'questionnaire':
            $questionnaireController = new QuestionnaireController();
            $acceptesController = new AcceptesController();

            switch ($action) {
                case 'creation':
                    $questionnaireController->ajouterQuestionnaire();
                    break;
                case 'lister':
                    $questionnaireController->listerQuestionnaires();
                    break;
                case 'resultats':
                    $questionnaireController->resultatsQuestionnaire();
                    break;
                case 'enregistrer':
                    if ($questionnaireController->enregistrer()) {
                    $acceptesController->enregistrer(); }
                    break;
                case 'repondre':
                    $id = isset($_GET['id']) ? $_GET['id'] : null;
                    $questionnaireController->repondre($id);
                    break;
                case 'supprimer':
                    $id = isset($_GET['id']) ? $_GET['id'] : null;
                    $questionnaireController->supprimer($id);
                    break;
            }
            break;


        case 'connexion':
            require_once(__DIR__.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'page_connexion.php');
            break;
    }

    
    require_once(__DIR__.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'footer.php');