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
        $ajoutOk = false;
        // Partie de test
        foreach ($_POST as $id => $valeur) {
            echo "$id -> $valeur <br>";
        }
        return;

        $id_utilisateur = isset($_SESSION['id_utilisateur']) ? $_SESSION['id_utilisateur'] : null;
        if (isset($_POST['json_reponses'])) {
            $jsonreponse = json_decode($_POST['json_reponses'], true);
            if (is_array($jsonreponse)) {
                foreach ($jsonreponse as $reponseData) {
                    $id_choix = isset($reponseData['id_choix']) ? $reponseData['id_choix'] : null;
                    $reponse = isset($reponseData['reponse']) ? $reponseData['reponse'] : null;
                    echo "debug : \$id_choix = $id_choix , \$reponse = $reponse ";
                    if (isset($id_utilisateur) && isset($id_choix) && isset($reponse)) {
                        $ajoutOk = $this->reponses_utilisateurModel->createReponse($id_utilisateur, $id_choix, $reponse);
                    }
                    if (!$ajoutOk) {
                        return false;
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
        return $ajoutOk;
    }

    public function resultatsQuestionnaire() {
        require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'resultatsQuestionnaire.php');
    }
}