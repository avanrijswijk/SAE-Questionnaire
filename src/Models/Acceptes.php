<?php

require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Models'.DIRECTORY_SEPARATOR.'Database.php');


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

//    public function update($id_questionnaire, $id_utilisateur) {
//        $query = "UPDATE acceptes 
//                  SET <ajouter les nouveaux aruguments ici>
//                  WHERE id_questionnaire = :id_questionnaire AND id_utilisateur = :id_utilisateur";
//
//        $stmt = $this->conn->prepare($query);
//
//        $stmt->bindParam(':id_questionnaire', $id_questionnaire);
//        $stmt->bindParam(':id_utilisateur', $id_utilisateur);
//
//        $stmt->execute();
//    }

    public function delete($id_questionnaire, $id_utilisateur) {
        $query = "DELETE FROM acceptes WHERE id_questionnaire = :id_questionnaire AND id_utilisateur = :id_utilisateur";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_questionnaire', $id_questionnaire);
        $stmt->bindParam(':id_utilisateur', $id_utilisateur);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }   
}