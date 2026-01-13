<?php

namespace App\Controllers;

use App\Models\Choix_possible;

class Choix_possibleController {

    private $choixPossibleModel;
    public function __construct() {
        $choixPossibleModel = new Choix_possible();
        $this->choixPossibleModel = $choixPossibleModel;
    }

    public function enregistrer() {
        $id_question = isset($_POST['id_question']) ? $_POST['id_question'] : null;
        $texte = isset($_POST['texte']) ? $_POST['texte'] : null;

        if (isset($id_question) && isset($texte)) {
            $ajoutOk = $this->choixPossibleModel->createChoix($id_question, $texte); //... pour excéuter cette ligne par utilisateur
        }

        if (!$ajoutOk) {
            echo 'Erreur lors de l\'enregistrement deschoix de la question :' . $id_question;
        }
    }

    //enregistre les choix à partir d'une liste de type string[]
    public function enregistrerListe($ChoixListe, $id_question) {
        if (is_array($ChoixListe)) {
            foreach ($ChoixListe as $choix) {
                if (isset($id_question) && (isset($choix) || is_null($choix))) {
                    $ajoutOk = $this->choixPossibleModel->createChoix($id_question, $choix);
                    if (!$ajoutOk) {
                        echo 'Erreur lors de l\'enregistrement des choix de la question :' . $id_question;
                    }
                }
            }
            return $ajoutOk;
        } else {
            echo 'Données de choix invalides.';
            return false;
        }
    }

    public function supprimer() {
        $id = isset($_POST['id']) ? $_POST['id'] : null;

        if (isset($id)) {
            $suppressionOk = $this->choixPossibleModel->delete($id);
        }

        if (!$suppressionOk) {
            echo 'Erreur lors de la suppression du choix de la question :' . $id;
        }
    }

    public function getChoixDeQuestion($id_question) {
        $choix = $this->choixPossibleModel->getChoixDeQuestion($id_question);

        return $choix;
    }
}

    