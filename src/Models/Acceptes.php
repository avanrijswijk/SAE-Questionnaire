<?php

namespace App\Models;

use PDO;

class Acceptes {

    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAllAcceptes() {
        $query = "SELECT * FROM acceptes";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAcceptesParticipants($id_questionnaire) {
        $query = "SELECT * FROM acceptes WHERE id_questionnaire = :id_questionnaire";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_questionnaire', $id_questionnaire);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAcceptesParticipeA($id_utilisateur) {
        $query = "SELECT * FROM acceptes WHERE id_utilisateur = :id_utilisateur";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_utilisateur', $id_utilisateur);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

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

    public function createAcceptes($id_questionnaire, $id_utilisateur) {
        $query = "INSERT INTO acceptes (id_questionnaire, id_utilisateur) 
        VALUES (:id_questionnaire, :id_utilisateur)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id_questionnaire', $id_questionnaire);
        $stmt->bindParam(':id_utilisateur', $id_utilisateur);

        $stmt->execute();

        return $this->conn->lastInsertId();
    }

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

    public function delete($id_questionnaire, $id_utilisateur) {
        $query = "DELETE FROM acceptes WHERE id_questionnaire = :id_questionnaire AND id_utilisateur = :id_utilisateur";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_questionnaire', $id_questionnaire);
        $stmt->bindParam(':id_utilisateur', $id_utilisateur);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }   

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

    public function nombreParticipant($id_questionnaire) {
        $query = "SELECT COUNT(*) as count FROM acceptes WHERE id_questionnaire = :id_questionnaire";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_questionnaire', $id_questionnaire);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }

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