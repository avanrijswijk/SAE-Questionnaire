<?php

namespace App\Controllers;

use App\Models\Reponses_utilisateur;
use App\Models\Acceptes;
use App\Models\Questionnaire;

class ReponsesUtilisateurController {

    private $reponses_utilisateurModel;
    private $acceptesModel;
    private $questionnaireModel;

    /**
     * Constructeur de la classe ReponsesUtilisateurController.
     * Initialise les instances des modèles nécessaires.
     */
    public function __construct() {
        $reponses_utilisateurModel = new Reponses_utilisateur();
        $this->reponses_utilisateurModel = $reponses_utilisateurModel;
        $acceptesModel = new Acceptes();
        $this->acceptesModel = $acceptesModel;
        $questionnaireModel = new Questionnaire();
        $this->questionnaireModel = $questionnaireModel;
    }

    /**
     * Enregistre les réponses de l'utilisateur à un questionnaire.
     * Traite les données POST, crée les réponses dans la base de données,
     * et marque le questionnaire comme répondu.
     *
     * @return bool True si l'enregistrement réussit, false sinon.
     */
    public function enregistrer() {
        $ajoutOk = false;

        if (is_array($_POST) && !empty($_POST)) {
            $id_utilisateur = $_SESSION['id_utilisateur'] ?? null;
            if (!isset($id_utilisateur)) {
                $id_utilisateur = 1; // Utilisateur non connecté pour les tests
            }

            $dernier_id_choix = null;

            foreach ($_POST as $key => $value) {
                // On ne traite que les clés qui commencent par "choix-"
                if (strpos($key, 'choix-') !== 0) {
                    continue;
                }

                $id_choix = substr($key, strlen('choix-'));
                $reponse  = $value;

                $id_utilisateur = $_SESSION['cas_user'] ?? null;
                
                if (!isset($id_utilisateur)) {
                    $id_utilisateur = 'anonyme';
                }

                $dernier_id_choix = $id_choix;

                $ajoutOk = $this->reponses_utilisateurModel->creerReponse($id_utilisateur, $id_choix, $reponse);
                if (!$ajoutOk) {
                    echo "echec de l'insertion.";
                    return false;
                }
            }
        } else {
            echo 'Données de réponses invalides.';
        }

        if ($ajoutOk && $dernier_id_choix !== null) {
            $this->acceptesModel->repondre($id_utilisateur , $this->questionnaireModel->getQuestionnaireParIdReponse($dernier_id_choix));
            require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'home.php');
        } else {
            echo 'Erreur lors de l\'enregistrement des réponses.';
        }
        return $ajoutOk;
    }

    /**
     * Affiche la vue des résultats du questionnaire.
     */
    public function resultatsQuestionnaire() {
        require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'resultatsQuestionnaire.php');
    }
}