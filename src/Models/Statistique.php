<?php

namespace App\Models;

use PDO;

class Statistique {

    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Récupère les données pour les graphiques (Radio & Checkbox)
     */
    public function getStatsQuestionsFermees($id_questionnaire) {
        $sql = "SELECT 
                    q.id AS id_question,
                    q.intitule AS titre_question,
                    q.type AS type_question,
                    cp.id AS id_choix,
                    cp.texte AS label,
                    COUNT(ru.id_choix) AS nb_votes
                FROM questions q
                JOIN choix_possible cp ON q.id = cp.id_question
                LEFT JOIN reponses_utilisateur ru ON cp.id = ru.id_choix
                WHERE q.id_questionnaire = ? 
                  AND q.type IN ('radio', 'checkbox')
                GROUP BY q.id, q.intitule, q.type, cp.id, cp.texte
                ORDER BY q.id, cp.id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id_questionnaire]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les textes saisis pour les questions ouvertes (Texte)
     */
    public function getStatsQuestionsOuvertes($id_questionnaire) {
        
        $sql = "SELECT 
                    q.id AS id_question,
                    q.intitule AS titre_question,
                    ru.reponse AS reponse_texte,
                    ru.date_reponse
                FROM questions q
                JOIN choix_possible cp ON q.id = cp.id_question
                JOIN reponses_utilisateur ru ON cp.id = ru.id_choix
                WHERE q.id_questionnaire = ? 
                  AND q.type = 'textfield'
                  AND ru.reponse IS NOT NULL 
                  AND ru.reponse != ''
                ORDER BY q.id, ru.date_reponse DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id_questionnaire]);
        
        $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $questions_texte = [];
        
        foreach ($resultats as $ligne) {
            $id_q = $ligne['id_question'];
            if (!isset($questions_texte[$id_q])) {
                $questions_texte[$id_q] = [
                    'titre' => $ligne['titre_question'],
                    'reponses' => []
                ];
            }
            $questions_texte[$id_q]['reponses'][] = $ligne['reponse_texte'];
        }
        
        return $questions_texte;
    }
}
?>