<?php

//session_start();


require_once(__DIR__.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'Controllers'.DIRECTORY_SEPARATOR.'QuestionnaireController.php');


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
                    $questionnaireController->enregistrer();
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