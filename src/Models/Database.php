<?php

namespace App\Models;

use PDO;
use PDOException;

Class Database 
{ 
    private $conn;

    public function getConnection()
    {
        if ($this->conn === null) {
            // Lire les variables d'environnement avec des valeurs par défaut
            $host = getenv('DB_HOST') ?: 'localhost';
            $port = getenv('DB_PORT') ?: '3307';
            $dbName = getenv('DB_NAME') ?: 'questionnaire_app';
            $username = getenv('DB_USER') ?: 'root';
            $password = getenv('DB_PASS') ?: '';

            $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $dbName);

            try {
                $this->conn = new PDO($dsn, $username, $password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $exception) {
                echo 'Erreur de connexion : ' . $exception->getMessage();
            }
        }

        return $this->conn;
    }
}