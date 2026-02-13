<?php

namespace App\Controllers;

use App\Models\Acceptes;

class AcceptesController {

    private $acceptesModel;

    /**
     * Constructeur pour AcceptesController.
     * Initialise l'instance du modèle Acceptes.
     */
    public function __construct() {
        $acceptesModel = new Acceptes();
        $this->acceptesModel = $acceptesModel;
    }
    
    /**
     * Enregistre un utilisateur en tant que participant à un questionnaire.
     * Récupère id_questionnaire et id_utilisateur depuis les données POST,
     * ajoute le participant en utilisant le modèle, et redirige vers la page d'accueil en cas de succès.
     */
    public function enregistrer() {
        $id_questionnaire = isset($_POST['id_questionnaire']) ? $_POST['id_questionnaire'] : null;
        $id_utilisateur = isset($_POST['id_utilisateur']) ? $_POST['id_utilisateur'] : null; //pour plus tard, permettre de récuppérer plusieurs utilisateurs...

        if (isset($id_questionnaire) && isset($id_utilisateur)) {
            $ajoutOk = $this->acceptesModel->creerAcceptes($id_questionnaire, $id_utilisateur); //... pour excéuter cette ligne par utilisateur
        }

        if ($ajoutOk) {
            require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'home.php');
        } else {
            echo 'Erreur lors de l\'enregistrement des participants.';
        }
    }

    /**
     * Supprime un utilisateur des participants d'un questionnaire.
     * Récupère id_questionnaire et id_utilisateur depuis les données POST,
     * supprime le participant en utilisant le modèle, et redirige vers la page d'accueil en cas de succès.
     */
    public function supprimer() {
        $id_questionnaire = isset($_POST['id_questionnaire']) ? $_POST['id_questionnaire'] : null;
        $id_utilisateur = isset($_POST['id_utilisateur']) ? $_POST['id_utilisateur'] : null;

        if (isset($id_questionnaire) && isset($id_utilisateur)) {
            $suppressionOk = $this->acceptesModel->supprimer($id_questionnaire, $id_utilisateur);
        }

        if ($suppressionOk) {
            require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'home.php');
        } else {
            echo 'Erreur lors de la suppression des participants.';
        }
    }

    /**
     * Liste tous les participants pour un questionnaire donné.
     *
     * @param int $id_questionnaire L'ID du questionnaire.
     * @return array Liste des participants.
     */
    public function listerParticipants($id_questionnaire) {
        $participants = $this->acceptesModel->getAcceptesPar(['id_questionnaire' => $id_questionnaire]);

        return $participants;
    }

    /**
     * Vérifie si un utilisateur est participant à un questionnaire donné.
     *
     * @param int $id_questionnaire L'ID du questionnaire.
     * @param int $id_utilisateur L'ID de l'utilisateur.
     * @return bool Vrai si l'utilisateur est participant, faux sinon.
     */
    public function estParticipant($id_questionnaire, $id_utilisateur) {
        $participant = $this->acceptesModel->getAcceptesPar([
            'id_questionnaire' => $id_questionnaire,
            'id_utilisateur' => $id_utilisateur
        ]);

        return !empty($participant);
    }

    /**
     * Liste tous les questionnaires auxquels l'utilisateur actuel participe.
     * Utilise l'ID utilisateur de la session pour récupérer les questionnaires participés.
     *
     * @return array Liste des questionnaires auxquels l'utilisateur participe.
     */
    public function listerQuestionnaires() {
        $questionnaires = $this->acceptesModel->getAcceptesParticipeA($_SESSION['id_utilisateur']);

        return $questionnaires;
    }

    /**
     * Retourne une chaîne représentant le nombre de réponses sur le total des participants pour un questionnaire.
     *
     * @param int $id_questionnaire L'ID du questionnaire.
     * @return string Chaîne formatée comme "compte/total".
     */
    public function nombreReponduText($id_questionnaire) {
       $compteur = $this->acceptesModel->compteLesRepondu($id_questionnaire);
       $total = $this->acceptesModel->nombreParticipant($id_questionnaire);

       return $compteur . '/' . $total;
    }
}
   