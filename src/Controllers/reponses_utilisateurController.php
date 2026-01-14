<?php

namespace App\Controllers;

use App\Models\Reponses_utilisateur;
use App\Models\Acceptes;

class Reponses_utilisateurController {

    private $reponses_utilisateurModel;
    private $acceptesModel;

    public function __construct() {
        $reponses_utilisateurModel = new Reponses_utilisateur();
        $this->reponses_utilisateurModel = $reponses_utilisateurModel;
        $acceptesModel = new Acceptes();
        $this->acceptesModel = $acceptesModel;
    }

    public function enregistrer() {
        $ajoutOk = false;

        if (is_array($_POST) && !empty($_POST)) {
            foreach ($_POST as $id => $valeur) {
                $id_choix = $id;
                $reponse  = $valeur;
                $id_utilisateur = $_SESSION['id_utilisateur'] ?? null;
                
                if (!isset($id_utilisateur)) {
                    $id_utilisateur = 1; // Utilisateur non connecté pour les testes
                }
                $ajoutOk = $this->reponses_utilisateurModel->createReponse($id_utilisateur, $id_choix, $reponse);
                if (!$ajoutOk) {
                    echo "echec de l'insertion.";
                    return false;
                }
            }
        } else {
            echo 'Données de réponses invalides.';
        }

        if ($ajoutOk) {
            $this->acceptesModel->repondre($_GET['id'], $id_utilisateur);
            require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'home.php');
        } else {
            echo 'Erreur lors de l\'enregistrement des réponses.';
        }
        return $ajoutOk;
    }

    public function resultatsQuestionnaire() {
        require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'resultatsQuestionnaire.php');
    }
}