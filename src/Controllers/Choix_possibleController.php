<?php

namespace App\Controllers;

use App\Models\Choix_possible;

class Choix_possibleController {

    private $choixPossibleModel;

    /**
     * Constructeur de la classe Choix_possibleController.
     * Initialise l'instance du modèle Choix_possible.
     */
    public function __construct() {
        $choixPossibleModel = new Choix_possible();
        $this->choixPossibleModel = $choixPossibleModel;
    }

    /**
     * Enregistre un choix possible pour une question.
     * Utilise les données POST pour l'id_question et le texte.
     */
    public function enregistrer() {
        $id_question = isset($_POST['id_question']) ? $_POST['id_question'] : null;
        $texte = isset($_POST['texte']) ? $_POST['texte'] : null;

        if (isset($id_question) && isset($texte)) {
            $ajoutOk = $this->choixPossibleModel->creerChoix($id_question, $texte); //... pour excéuter cette ligne par utilisateur
        }

        if (!$ajoutOk) {
            echo 'Erreur lors de l\'enregistrement deschoix de la question :' . $id_question;
        }
    }

    /**
     * Enregistre une liste de choix possibles pour une question.
     *
     * @param array $ChoixListe Liste des choix à enregistrer.
     * @param int $id_question ID de la question.
     * @return bool True si l'enregistrement réussit, false sinon.
     */
    public function enregistrerListe($ChoixListe, $id_question) {
        if (is_array($ChoixListe)) {
            foreach ($ChoixListe as $choix) {
                if (isset($id_question) && (isset($choix) || is_null($choix))) {
                    $ajoutOk = $this->choixPossibleModel->creerChoix($id_question, $choix);
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

    /**
     * Supprime un choix possible.
     * Utilise l'ID du choix depuis POST.
     */
    public function supprimer() {
        $id = isset($_POST['id']) ? $_POST['id'] : null;

        if (isset($id)) {
            $suppressionOk = $this->choixPossibleModel->supprimer($id);
        }

        if (!$suppressionOk) {
            echo 'Erreur lors de la suppression du choix de la question :' . $id;
        }
    }

    /**
     * Récupère les choix possibles pour une question donnée.
     *
     * @param int $id_question ID de la question.
     * @return array Liste des choix.
     */
    public function getChoixDeQuestion($id_question) {
        $choix = $this->choixPossibleModel->getChoixDeQuestion($id_question);

        return $choix;
    }
}
