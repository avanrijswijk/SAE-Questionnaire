<?php

namespace App\Models;

use PDO;

class Reponses_utilisateur {

    private $conn;

    /**
     * Constructeur de la classe Reponses_utilisateur.
     * Initialise la connexion à la base de données.
     */
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Récupère toutes les réponses des utilisateurs.
     *
     * @return array Liste de toutes les réponses.
     */
    public function getTousLesReponses() {
        $query = "SELECT * FROM reponses_utilisateur";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère une réponse spécifique d'un utilisateur pour un choix.
     *
     * @param int $id_utilisateur L'identifiant de l'utilisateur.
     * @param int $id_choix L'identifiant du choix.
     * @return array Les données de la réponse.
     */
    public function getReponse($id_utilisateur, $id_choix) {
        $query = "SELECT * FROM reponses_utilisateur WHERE id_utilisateur = :id_utilisateur AND id_choix = :id_choix";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_utilisateur', $id_utilisateur);
        $stmt->bindParam(':id_choix', $id_choix);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les réponses basées sur des paramètres donnés.
     *
     * @param array $params Tableau associatif des paramètres de recherche.
     * @return array Liste des réponses correspondant aux paramètres.
     */
    public function getReponsePar(array $params) {
        $query = "SELECT * FROM reponses_utilisateur WHERE ". implode(' AND ',array_map(function($key) {
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
     * Crée une nouvelle réponse d'utilisateur pour un choix.
     *
     * @param int $id_utilisateur L'identifiant de l'utilisateur.
     * @param int $id_choix L'identifiant du choix.
     * @param string $reponse La réponse donnée.
     * @return bool Vrai si l'insertion a réussi, faux sinon.
     */
    public function creerReponse($id_utilisateur, $id_choix, $reponse) {
        $query = "INSERT INTO reponses_utilisateur (id_utilisateur, id_choix, reponse, date_reponse) 
        VALUES (:id_utilisateur, :id_choix, :reponse, NOW())";
       
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id_utilisateur', $id_utilisateur);
        $stmt->bindParam(':id_choix', $id_choix);
        $stmt->bindParam(':reponse', $reponse);
        return $stmt->execute();
    }

    /**
     * Supprime une réponse d'utilisateur pour un choix.
     *
     * @param int $id_utilisateur L'identifiant de l'utilisateur.
     * @param int $id_choix L'identifiant du choix.
     * @return bool Vrai si la suppression a réussi, faux sinon.
     */
    public function supprimer($id_utilisateur, $id_choix) {
        $query = "DELETE FROM reponses_utilisateur WHERE id_utilisateur = :id_utilisateur AND id_choix = :id_choix";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_utilisateur', $id_utilisateur);
        $stmt->bindParam(':id_choix', $id_choix);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    /**
     * Récupère les réponses pour un questionnaire au format CSV.
     * Joint les tables pour obtenir les questions, choix et réponses.
     *
     * @param int $id_questionnaire L'identifiant du questionnaire.
     * @return array Liste des réponses formatées pour CSV.
     */
    public function getReponsePourCSV($id_questionnaire) {
        $query = "SELECT 
                    qt.intitule AS question,
                    CONCAT(u.prenom, ' ', u.nom) AS repondant,
                    cp.texte AS choix,
                    ru.reponse AS reponse_libre
                FROM reponses_utilisateur ru
                JOIN utilisateurs u ON u.identifiant = ru.id_utilisateur
                JOIN choix_possible cp ON cp.id = ru.id_choix
                JOIN questions qt ON qt.id = cp.id_question
                WHERE qt.id_questionnaire = :id
                ORDER BY ru.date_reponse ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id_questionnaire); // ✔️ correction ici
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère la liste des répondants distincts pour un questionnaire donné,
     * avec la date de leur dernière réponse.
     *
     * @param int $id_questionnaire L'identifiant du questionnaire.
     * @return array Liste des répondants (id_utilisateur, date_reponse).
     */
    public function getRepondantsParQuestionnaire($id_questionnaire) {
        $query = "SELECT r.id_utilisateur, u.nom, u.prenom, MAX(r.date_reponse) as date_reponse
                  FROM questionnaires qtr
                  JOIN questions qt ON qt.id_questionnaire = qtr.id
                  JOIN choix_possible c ON c.id_question = qt.id
                  JOIN reponses_utilisateur r ON r.id_choix = c.id
                  LEFT JOIN utilisateurs u ON u.identifiant = r.id_utilisateur
                  WHERE qtr.id = :id_questionnaire
                  GROUP BY r.id_utilisateur, u.nom, u.prenom
                  ORDER BY date_reponse DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_questionnaire', $id_questionnaire);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Vérifie si un utilisateur a déjà répondu à un questionnaire donné.
     *
     * @param int $id_questionnaire L'identifiant du questionnaire.
     * @param int $id_utilisateur L'identifiant de l'utilisateur.
     * @return bool Vrai si l'utilisateur a déjà répondu faux sinon.
     */
    public function aDejaRepondu($id_questionnaire, $id_utilisateur) {
        $query = "SELECT COUNT(*) as nb_reponses
                  FROM reponses_utilisateur r
                  JOIN choix_possible c ON r.id_choix = c.id
                  JOIN questions q ON c.id_question = q.id
                  WHERE q.id_questionnaire = :id_questionnaire 
                  AND r.id_utilisateur = :id_utilisateur";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_questionnaire', $id_questionnaire);
        $stmt->bindParam(':id_utilisateur', $id_utilisateur);
        $stmt->execute();
        
        $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return ($resultat['nb_reponses'] > 0);
    }

    /**
     * Récupère toutes les réponses pour un questionnaire spécifique.
     * Joint les tables pour obtenir les réponses liées au questionnaire.
     *
     * @param int $id_questionnaire L'identifiant du questionnaire.
     * @return array Liste des réponses pour le questionnaire.
     */
    public function getReponseParIdQuestionnaire($id_questionnaire) {
        $query = "SELECT r.* 
                FROM questionnaires qtnaire
                JOIN questions qt ON qt.id_questionnaire = qtnaire.id 
                JOIN choix_possible c ON c.id_question = qt.id
                JOIN reponses_utilisateur r ON r.id_choix = c.id
                WHERE qtnaire.id = :id_questionnaire
                ORDER BY r.id_choix, r.id_utilisateur";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_questionnaire', $id_questionnaire);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère l'identifiant de la dernière insertion.
     *
     * @return int L'identifiant de la dernière insertion.
     */
    public function getIdDerniereInsertion() {
        return $this->conn->lastInsertId();
    }

}