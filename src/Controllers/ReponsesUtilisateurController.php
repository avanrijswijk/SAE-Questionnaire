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
            $id_utilisateur = $_SESSION['cas_user'] ?? 'anonyme';
            $dernier_id_choix = null;

            foreach ($_POST as $key => $value) {
                
                if (strpos($key, 'choix-') === 0) {
                    $id_choix = substr($key, strlen('choix-'));
                    $reponse  = trim($value);
                    
                    if (!empty($reponse)) {
                        $dernier_id_choix = $id_choix;
                        $ajoutOk = $this->reponses_utilisateurModel->creerReponse($id_utilisateur, $id_choix, $reponse);
                    }
                } 
                
                else if (strpos($key, 'question-') === 0) {
                    
                    if (is_array($value)) {
                        foreach ($value as $id_choix_coche) {
                            $dernier_id_choix = $id_choix_coche;
                            $ajoutOk = $this->reponses_utilisateurModel->creerReponse($id_utilisateur, $id_choix_coche, "Coché");
                        }
                    } else {
                        $id_choix = $value;
                        $dernier_id_choix = $id_choix;
                        $ajoutOk = $this->reponses_utilisateurModel->creerReponse($id_utilisateur, $id_choix, "Sélectionné");
                    }
                }
            }
        } else {
            echo 'Données de réponses invalides.';
            return false;
        }

        if ($ajoutOk && $dernier_id_choix !== null) {
            $id_questionnaire = $this->questionnaireModel->getQuestionnaireParIdReponse($dernier_id_choix);
            if ($id_questionnaire != -1) {
                $this->acceptesModel->repondre($id_utilisateur, $id_questionnaire);
            }
            header("Location: ./?c=home");
            exit();
        } else {
            echo 'Erreur : Aucune réponse enregistrée.';
        }
        return $ajoutOk;
    }
}