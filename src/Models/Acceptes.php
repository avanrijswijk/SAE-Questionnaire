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

    public function createQuestionnaire($titre, $id_createur, $date_expiration, $code) {
        $query = "INSERT INTO acceptes (titre, id_createur, date_expiration, date_creation, code) 
        VALUES (:titre, :id_createur, :date_expiration, NOW(), :code)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':titre', $titre);
        $stmt->bindParam(':id_createur', $id_createur);
        $stmt->bindParam(':date_expiration', $date_expiration);
        $stmt->bindParam(':code', $code);

        $stmt->execute();

        return $this->conn->lastInsertId();
    }

    public function update($id, $titre, $date_expiration) {
        $query = "UPDATE acceptes 
                  SET titre = :titre, date_expiration = :date_expiration,
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':titre', $titre);
        $stmt->bindParam(':date_expiration', $date_expiration);
        $stmt->bindParam(':id', $id);

        $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM acceptes WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }    


}