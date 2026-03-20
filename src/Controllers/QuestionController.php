<?php

namespace App\Controllers;

use App\Models\Question;
use App\Controllers\Choix_possibleController;

class QuestionController {

    private $questionModel;
    private $choix_possibleController;

    /**
     * Constructeur de la classe QuestionController.
     * Initialise les instances du modèle Question et du contrôleur Choix_possibleController.
     */
    public function __construct() {
        $questionModel = new Question();
        $this->questionModel = $questionModel;
        $this->choix_possibleController = new Choix_possibleController();
    }

    /**
     * Enregistre une liste de questions pour un questionnaire donné.
     * Traite les données JSON depuis POST, crée les questions et leurs choix associés.
     *
     * @param int $id_questionnaire ID du questionnaire.
     * @return bool True si l'enregistrement réussit, false sinon.
     */
    public function enregistrerQuestions($id_questionnaire) {
        $ajoutOk = true;
        if(!isset($id_questionnaire)) {
            $id_questionnaire=-1;
        }
        if (isset($_POST['liste-questions'])) {
            $jsonQuestion = json_decode($_POST['liste-questions'], true);
            if (is_array($jsonQuestion)) {
                
                foreach ($jsonQuestion as $questionData) {
                    $intitule = isset($questionData['intitule']) ? $questionData['intitule'] : null;
                    $type = isset($questionData['type']) ? $questionData['type'] : null;
                    $position = isset($questionData['position']) ? $questionData['position'] : null;
                    $est_obligatoire = isset($questionData['est_obligatoire']) ? $questionData['est_obligatoire'] : null;
                    $choixListe = isset($questionData['choix']) ? $questionData['choix'] : null;
                    if ($est_obligatoire == 'true') {
                        $est_obligatoire = 1;
                    } else {
                        $est_obligatoire = 0;
                    }
                    
                    $ajoutOk = $this->questionModel->creerQuestion($id_questionnaire, $intitule, $type, $position, $est_obligatoire);
                    if (!$ajoutOk) {
                        echo 'Erreur lors de l\'enregistrement des questions.';
                    }
                    $ajoutOk = $this->choix_possibleController->enregistrerListe($choixListe, $this->questionModel->getIdDerniereInsertion());
                    if (!$ajoutOk) {
                        echo 'Erreur lors de l\'enregistrement des choix pour la question :' . $intitule;
                    }
                }
                return $ajoutOk;
            } else {
                echo 'Données de questions invalides.';
            }
        }
        return $ajoutOk;
    }

    /**
     * Supprime une question si elle appartient au questionnaire spécifié.
     *
     * @param int $id ID de la question.
     * @param int $id_questionnaire ID du questionnaire.
     * @return bool True si la suppression réussit, false sinon.
     */
    public function supprimer($id, $id_questionnaire) {
        if (empty($id) || empty($id_questionnaire)) {
            return false;
        }

        $question = $this->questionModel->getQuestion($id);

        if ($question && $question['id_questionnaire'] == $id_questionnaire) {
            return $this->questionModel->supprimer($id);
        }

        return false;
    }
}

    