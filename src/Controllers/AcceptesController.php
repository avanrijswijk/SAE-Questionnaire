<?php

require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Models'.DIRECTORY_SEPARATOR.'Acceptes.php');

class AcceptesController {

    private $acceptesModel;
    public function __construct() {
        $acceptesModel = new Acceptes();
        $this->acceptesModel = $acceptesModel;
    }

    public function enregistrer() {
        $id_questionnaire = isset($_POST['id_questionnaire']) ? $_POST['id_questionnaire'] : null;
        $id_utilisateur = isset($_POST['id_utilisateur']) ? $_POST['id_utilisateur'] : null; //pour plus tard, permettre de récuppérer plusieurs utilisateurs...

        if (isset($id_questionnaire) && isset($id_utilisateur)) {
            $ajoutOk = $this->acceptesModel->createAcceptes($id_questionnaire, $id_utilisateur); //... pour excéuter cette ligne par utilisateur
        }

        if ($ajoutOk) {
            require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'home.php');
        } else {
            echo 'Erreur lors de l\'enregistrement des participants.';
        }
    }

    public function supprimer() {
        $id_questionnaire = isset($_POST['id_questionnaire']) ? $_POST['id_questionnaire'] : null;
        $id_utilisateur = isset($_POST['id_utilisateur']) ? $_POST['id_utilisateur'] : null;

        if (isset($id_questionnaire) && isset($id_utilisateur)) {
            $suppressionOk = $this->acceptesModel->delete($id_questionnaire, $id_utilisateur);
        }

        if ($suppressionOk) {
            require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'home.php');
        } else {
            echo 'Erreur lors de la suppression des participants.';
        }
    }

}

    