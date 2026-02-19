<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Liste des domaines considérés comme "local"
$whitelist_local = array(
    '127.0.0.1',
    '::1',
    'localhost'
);

if (in_array($_SERVER['SERVER_NAME'], $whitelist_local)) {
    // --- MODE LOCAL ---
    // On simule un utilisateur connecté
    if (!isset($_SESSION['cas_user'])) {
        $_SESSION['cas_user'] = 'etudiant_local';
        $_SESSION['cas_prenom'] = 'Jean'; 
        $_SESSION['cas_nom'] = 'Dupont'; 
        $_SESSION['cas_email'] = 'jean.dupont@sae.com';
        $_SESSION['cas_groupes'] = ['groupe-etudiants-hors-doctorants', 'iut-etudiants-info', 'iut-etudiants-limoges', 'tlin12-221', 'iut-etudiants-info-2a'];
    }
} else {
    // --- MODE SERVEUR (IUT) ---
    // Activation de la sécurité CAS
    require_once 'config.php';
    // Vérification Consentement (Bloquant si pas accepté)
    require_once 'includes/onboarding.php';
}

require 'vendor/autoload.php';
require_once(__DIR__.DIRECTORY_SEPARATOR.'bootstrap.php');

use App\Controllers\QuestionnaireController;
use App\Controllers\AcceptesController;
use App\Controllers\QuestionController;
use App\Controllers\Reponses_utilisateurController;
use App\Controllers\Choix_possibleController;



// routage simple (normaliser en minuscules)
$controller = isset($_GET['c'])? strtolower($_GET['c']) : 'home';
$action = isset($_GET['a']) ? strtolower($_GET['a']) : 'lister';
$isExport = ($controller === 'questionnaire' && $action === 'exporter');

// ajout de l'en tête
if (!$isExport) {
require_once(__DIR__.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'header.php');
}

    switch ($controller) {

        case 'home':
            require_once(__DIR__.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'Controllers'.DIRECTORY_SEPARATOR.'homeController.php');
            break;

        case 'profil':
            require_once(__DIR__.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'profil.php');
            break;

        case 'mentionslegales':
            require_once(__DIR__.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'mentionsLegales.php');
            break;

        case 'questionnaire':
            $questionnaireController = new QuestionnaireController();
            $acceptesController = new AcceptesController();
            $reponses_utilisateurController = new Reponses_utilisateurController();
            $questionController = new QuestionController();

            switch ($action) {
                case 'creation':
                    $questionnaireController->ajouterQuestionnaire();
                    break;
                case 'lister':
                    $questionnaireController->listerQuestionnaires();
                    break;
                case 'resultats':
                    $reponses_utilisateurController->resultatsQuestionnaire();
                    break;
                case 'exporter':
                    $questionnaireController->exporterEnCSV();
                    break;
                case 'supprimer':
                    $questionnaireController->supprimer();
                    break;
                case 'enregistrer':
                    if ($questionnaireController->enregistrerQuestionnaire()) {
                        $questionController->enregistrerQuestions($questionnaireController->getIdDerniereInsertion());
                    }
                    break;
                case 'enregistrer-reponses' :
                    $reponses_utilisateurController->enregistrer();
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

    if (!$isExport) {
    require_once(__DIR__.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'footer.php');
    }