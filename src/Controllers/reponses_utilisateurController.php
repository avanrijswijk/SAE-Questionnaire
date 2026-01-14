<?php

namespace App\Controllers;

use App\Models\Reponses_utilisateur;

class Reponses_utilisateurController {

    private $reponses_utilisateurModel;

    public function __construct() {
        $reponses_utilisateurModel = new Reponses_utilisateur();
        $this->reponses_utilisateurModel = $reponses_utilisateurModel;
    }

    public function enregistrer() {
        // Partie de test
        foreach ($_POST as $id => $valeur) {
            echo "$id -> $valeur <br>";
        }
        return;

        $id_utilisateur = isset($_SESSION['id_utilisateur']) ? $_SESSION['id_utilisateur'] : null;
        if (isset($_POST['liste-reponses'])) {
            $jsonreponse = json_decode($_POST['liste-reponses'], true);

            if (is_array($jsonreponse)) {
                foreach ($jsonreponse as $reponseData) {
                    $id_choix = isset($reponseData['id_choix']) ? $reponseData['id_choix'] : null;
                    $reponse = isset($reponseData['reponse']) ? $reponseData['reponse'] : null;
                    if (isset($id_utilisateur) && isset($id_choix) && isset($reponse)) {
                        $ajoutOk = $this->reponses_utilisateurModel->createReponse($id_utilisateur, $id_choix, $reponse);
                    }
                }
            } else {
                echo 'Données de réponses invalides.';
            }
        }

        if ($ajoutOk) {
            require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'home.php');
        } else {
            echo 'Erreur lors de l\'enregistrement des réponses.';
        }
        
    }

    public function resultatsQuestionnaire() {
        require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'resultatsQuestionnaire.php');
    }
}