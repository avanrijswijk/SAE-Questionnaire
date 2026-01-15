<?php

namespace App\Models;

use PDO;

class Questionnaire {

    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAllQuestionnaires() {
        $query = "SELECT * FROM questionnaires";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getQuestionnaire($id) {
        $query = "SELECT * FROM questionnaires WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getQuestionnaireBy(array $params) {
        $query = "SELECT * FROM questionnaires WHERE ". implode(' AND ',array_map(function($key) {
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
        $query = "INSERT INTO questionnaires (titre, id_createur, date_expiration, date_creation, code) 
        VALUES (:titre, :id_createur, :date_expiration, NOW(), :code)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':titre', $titre);
        if (is_null($id_createur)) {
            $id_createur = 1; // utilisateur par défaut en attendant la posibiliter de gérer les utilisateurs
        } 
        $stmt->bindParam(':id_createur', $id_createur);
        $stmt->bindParam(':date_expiration', $date_expiration);
        if (is_null($code)) {
            $code = 'ACDC';
        }
        while ($this->existsCode($code)) {
            // génère un code aléatoire de 4 lettres meme si la colonne 'code' n'est pas limité à 4 en vu de potentielles extentions
            $code = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 4); 
        }
        $stmt->bindParam(':code', $code);

        $stmt->execute();

        return $this->conn->lastInsertId();
    }

    public function update($id, $titre, $date_expiration) {
        $query = "UPDATE questionnaires 
                  SET titre = :titre, date_expiration = :date_expiration,
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':titre', $titre);
        $stmt->bindParam(':date_expiration', $date_expiration);
        $stmt->bindParam(':id', $id);

        $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM questionnaires WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }    

    public function existsCode($code) { //true = code exists, false = code n'existe pas
        $query = "SELECT COUNT(*) as count FROM questionnaires WHERE code = :code";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':code', $code);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    public function getQuestionnairesByUserId($id_utilisateur) {
        $query = "SELECT * FROM questionnaires WHERE id IN (SELECT id_questionnaire FROM acceptes WHERE id_utilisateur = :id_utilisateur)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_utilisateur', $id_utilisateur);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getResults($id_questionnaire) {
        $query = "SELECT qt.intitule ,r.reponse
                  FROM questionnaires qtnaire
                  where qtnaire.id = :id_questionnaire
                  LEFT JOIN questions qt ON qt.id_questionnaire = qtnaire.id
                  left JOIN choix_possible c ON c.id_question = qt.id
                  left JOIN reponses r ON r.id_choix = c.id
                  group BY qt.id, r.id
                  ORDER BY qt.id, r.id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_questionnaire', $id_questionnaire);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }

    public function getTitreWithTireDuSixByID($id_questionnaire) {
        $query = "SELECT titre FROM questionnaires WHERE id = :id_questionnaire";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_questionnaire', $id_questionnaire);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!isset($result['titre'])) {
            echo "Questionnaire introuvable.";
            return;
        } else {
            $titre = $result['titre'];
            $titreDuSix = str_replace(' ', '-', $titre);
            return $titreDuSix;
        }
    }
}