<?php

namespace App\Models;

use PDO;

class Choix_possible {

    private $conn;

    /**
     * Constructeur de la classe Choix_possible.
     * Initialise la connexion à la base de données.
     */
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Récupère tous les choix possibles.
     *
     * @return array Liste de tous les choix possibles.
     */
    public function getAllChoix() {
        $query = "SELECT * FROM choix_possible";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les choix possibles pour une question spécifique.
     *
     * @param int $id_question L'identifiant de la question.
     * @return array Liste des choix pour la question.
     */
    public function getChoixDeQuestion($id_question) {
        $query = "SELECT * FROM choix_possible WHERE id_question = :id_question";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_question', $id_question);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les choix possibles basés sur des paramètres donnés.
     *
     * @param array $params Tableau associatif des paramètres de recherche.
     * @return array Liste des choix correspondant aux paramètres.
     */
    public function getChoixBy(array $params) {
        $query = "SELECT * FROM choix_possible WHERE ". implode(' AND ',array_map(function($key) {
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
     * Crée un nouveau choix possible pour une question.
     *
     * @param int $id_question L'identifiant de la question.
     * @param string $texte Le texte du choix.
     * @return int L'identifiant de la nouvelle insertion.
     */
    public function createChoix($id_question, $texte) {
        $query = "INSERT INTO choix_possible (id_question, texte) 
        VALUES (:id_question, :texte)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id_question', $id_question);
        $stmt->bindParam(':texte', $texte);

        $stmt->execute();

        return $this->conn->lastInsertId();
    }

    /**
     * Met à jour le texte d'un choix possible.
     *
     * @param int $id L'identifiant du choix.
     * @param string $texte Le nouveau texte du choix.
     * @return bool Vrai si la mise à jour a réussi, faux sinon.
     */
    public function update($id, $texte) {
        $query = "UPDATE choix_possible SET texte = :texte WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':texte', $texte);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * Supprime un choix possible.
     *
     * @param int $id L'identifiant du choix à supprimer.
     * @return bool Vrai si la suppression a réussi, faux sinon.
     */
    public function delete($id) {
        $query = "DELETE FROM choix_possible WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    /**
     * Compte le nombre de choix pour une question.
     *
     * @param int $id_question L'identifiant de la question.
     * @return int Le nombre de choix.
     */
    public function countChoix($id_question) {
        $query = "SELECT COUNT(*) as count FROM choix_possible WHERE id_question = :id_question";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_question', $id_question);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }

    /**
     * Récupère l'identifiant de la dernière insertion.
     *
     * @return int L'identifiant de la dernière insertion.
     */
    public function getLastInsertId() {
        return $this->conn->lastInsertId();
    }
}