<?php

namespace App\Models;

use PDO;

class Acceptes {

    private $conn;

    /**
     * Constructeur de la classe Acceptes.
     * Initialise la connexion à la base de données.
     */
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Récupère toutes les acceptations.
     *
     * @return array Liste de toutes les acceptations.
     */
    public function getAllAcceptes() {
        $query = "SELECT * FROM acceptes";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les acceptations pour un questionnaire spécifique.
     *
     * @param int $id_questionnaire L'identifiant du questionnaire.
     * @return array Les données d'acceptation pour le questionnaire.
     */
    public function getAcceptesParticipants($id_questionnaire) {
        $query = "SELECT * FROM acceptes WHERE id_questionnaire = :id_questionnaire";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_questionnaire', $id_questionnaire);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les acceptations pour un utilisateur spécifique.
     *
     * @param int $id_utilisateur L'identifiant de l'utilisateur.
     * @return array Les données d'acceptation pour l'utilisateur.
     */
    public function getAcceptesParticipeA($id_utilisateur) {
        $query = "SELECT * FROM acceptes WHERE id_utilisateur = :id_utilisateur";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_utilisateur', $id_utilisateur);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les acceptations basées sur des paramètres donnés.
     *
     * @param array $params Tableau associatif des paramètres de recherche.
     * @return array Liste des acceptations correspondant aux paramètres.
     */
    public function getAcceptesBy(array $params) {
        $query = "SELECT * FROM acceptes WHERE ". implode(' AND ',array_map(function($key) {
            return "$key = :$key";
        }, array_keys($params)));

        $stmt = $this->conn->prepare($query);

        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crée une nouvelle acceptation pour un utilisateur et un questionnaire.
     *
     * @param int $id_questionnaire L'identifiant du questionnaire.
     * @param int $id_utilisateur L'identifiant de l'utilisateur.
     * @return int L'identifiant de la nouvelle insertion.
     */
    public function createAcceptes($id_questionnaire, $id_utilisateur) {
        $query = "INSERT INTO acceptes (id_questionnaire, id_utilisateur) 
        VALUES (:id_questionnaire, :id_utilisateur)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id_questionnaire', $id_questionnaire);
        $stmt->bindParam(':id_utilisateur', $id_utilisateur);

        $stmt->execute();

        return $this->conn->lastInsertId();
    }

    /**
     * Met à jour le statut de réponse d'une acceptation.
     *
     * @param int $id_questionnaire L'identifiant du questionnaire.
     * @param int $id_utilisateur L'identifiant de l'utilisateur.
     * @param int $repondu Le statut de réponse (0 ou 1).
     */
    public function update($id_questionnaire, $id_utilisateur, $repondu) {
        $query = "UPDATE acceptes 
                  SET repondu = :repondu
                  WHERE id_questionnaire = :id_questionnaire AND id_utilisateur = :id_utilisateur";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id_questionnaire', $id_questionnaire);
        $stmt->bindParam(':id_utilisateur', $id_utilisateur);
        $stmt->bindParam(':repondu', $repondu);

        $stmt->execute();
    }

    /**
     * Supprime une acceptation pour un utilisateur et un questionnaire.
     *
     * @param int $id_questionnaire L'identifiant du questionnaire.
     * @param int $id_utilisateur L'identifiant de l'utilisateur.
     * @return bool Vrai si la suppression a réussi, faux sinon.
     */
    public function delete($id_questionnaire, $id_utilisateur) {
        $query = "DELETE FROM acceptes WHERE id_questionnaire = :id_questionnaire AND id_utilisateur = :id_utilisateur";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_questionnaire', $id_questionnaire);
        $stmt->bindParam(':id_utilisateur', $id_utilisateur);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    /**
     * Vérifie si un utilisateur a répondu à un questionnaire.
     *
     * @param int $id_utilisateur L'identifiant de l'utilisateur.
     * @param int $id_questionnaire L'identifiant du questionnaire.
     * @return bool Vrai si l'utilisateur a répondu, faux sinon.
     */
    public function asAnswered($id_utilisateur, $id_questionnaire) { //true = a déjà répondu, false = n'a pas répondu
        $query = "SELECT repondu FROM acceptes WHERE id_utilisateur = :id_utilisateur AND id_questionnaire = :id_questionnaire";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_utilisateur', $id_utilisateur);
        $stmt->bindParam(':id_questionnaire', $id_questionnaire);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result == 1) {
            return true;
        }
        return false;
    }

    /**
     * Compte le nombre d'utilisateurs ayant répondu à un questionnaire.
     *
     * @param int $id_questionnaire L'identifiant du questionnaire.
     * @return int Le nombre de réponses.
     */
    public function countRepondu($id_questionnaire) {
        $query = "SELECT COUNT(*) as count FROM acceptes WHERE id_questionnaire = :id_questionnaire AND repondu = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_questionnaire', $id_questionnaire);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }

    //alt
    //public function countRepondu($id_questionnaire) {
    //    $this->getAcceptesParticipants($id_questionnaire);
    //    $counter=0;
    //    foreach ($this->getAcceptesParticipants($id_questionnaire) as $part) {
    //        if ($part['repondu'] == 1) {
    //            $counter++;
    //        }
    //    }
    //    return $counter;
    //}

    /**
     * Compte le nombre total de participants à un questionnaire.
     *
     * @param int $id_questionnaire L'identifiant du questionnaire.
     * @return int Le nombre de participants.
     */
    public function nombreParticipant($id_questionnaire) {
        $query = "SELECT COUNT(*) as count FROM acceptes WHERE id_questionnaire = :id_questionnaire";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_questionnaire', $id_questionnaire);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }

    /**
     * Marque qu'un utilisateur a répondu à un questionnaire.
     *
     * @param int $id_utilisateur L'identifiant de l'utilisateur.
     * @param int $id_questionnaire L'identifiant du questionnaire.
     */
    public function repondre($id_utilisateur, $id_questionnaire) {
        $query = "UPDATE acceptes 
                  SET repondu = 1
                  WHERE id_utilisateur = :id_utilisateur AND id_questionnaire = :id_questionnaire";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id_utilisateur', $id_utilisateur);
        $stmt->bindParam(':id_questionnaire', $id_questionnaire);

        $stmt->execute();
    }
}