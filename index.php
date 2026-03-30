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
use App\Controllers\ReponsesUtilisateurController;
use App\Controllers\ChoixPossibleController;



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
            $reponses_utilisateurController = new ReponsesUtilisateurController();
            $questionController = new QuestionController();

            switch ($action) {
                case 'creation':
                    $id = isset($_GET['id']) ? $_GET['id'] : null;
                    $questionnaireController->ajouterQuestionnaire($id);
                    break;
                case 'lister':
                    $questionnaireController->listerQuestionnaires();
                    break;
                case 'resultats':
                    $reponses_utilisateurController->resultatsQuestionnaire();
                    break;
                case 'detail':
                    $id = isset($_GET['id']) ? $_GET['id'] : null;
                    $questionnaireController->detailQuestionnaire($id);
                    break;
                case 'changertitre':
                    $questionnaireController->changerTitre();
                    break;
                case 'exporter':
                    $questionnaireController->exporterEnCSV();
                    break;
                case 'supprimer':
                    $questionnaireController->supprimer();
                    break;
                case 'enregistrer':
                    if ($questionnaireController->enregistrerQuestionnaire()) {
                        $id = isset($_POST['id_questionnaire']) ? $_POST['id_questionnaire'] : $questionnaireController->getIdDerniereInsertion();
                        if (trim((string)$id) === "") {
                            $id = $questionnaireController->getIdDerniereInsertion();
                        }
                        $questionController->enregistrerQuestions($id);
                    }
                    break;
                case 'enregistrer-reponses' :
                    $reponses_utilisateurController->enregistrer();
                    break;
                case 'repondre':
                    $id = isset($_GET['id']) ? $_GET['id'] : null;
                    $questionnaireController->repondre($id);
                    break;
                case 'acceder-par-code':
                    $questionnaireController->accederParCode();
                    break;
                case 'supprimer':
                    $id = isset($_GET['id']) ? $_GET['id'] : null;
                    $questionnaireController->supprimer($id);
                    break;
                case 'analyse':
                    $id = isset($_GET['id']) ? $_GET['id'] : null;
                    $questionnaireController->analyseGraphique($id);
                    break;
                case 'modifier':
                    $questionnaireController->modifier();
            }
            break;


        case 'connexion':
            require_once(__DIR__.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'page_connexion.php');
            break;

            
        case 'erreur':
            switch ($action) {
                case 'droits':
                    require_once(__DIR__.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'Erreur'.DIRECTORY_SEPARATOR.'droits.php');
                    break;
                case 'deja-repondu':
                    require_once(__DIR__.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'Erreur'.DIRECTORY_SEPARATOR.'deja_repondu.php');
                    break;
                case '404':
                default:
                    require_once(__DIR__.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'Erreur'.DIRECTORY_SEPARATOR.'404.php');
                    break;
            }
            break;

        // Si le contrôleur tapé dans l'URL n'existe pas (ex: ?c=nimportequoi)
        default:
            require_once(__DIR__.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'Erreur'.DIRECTORY_SEPARATOR.'404.php');
            break;
    }

    if (!$isExport) {
    require_once(__DIR__.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'footer.php');
    }