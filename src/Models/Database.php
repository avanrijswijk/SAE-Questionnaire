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
            // Lire les variables d'environnement avec des valeurs par défaut.
            // Certaines installations remplissent $_ENV mais getenv() peut être vide,
            // donc on vérifie dans cet ordre : getenv -> $_ENV -> $_SERVER -> default.
            $get = function ($key, $default = null) {
                $val = getenv($key);
                if ($val !== false && $val !== null && $val !== '') {
                    return $val;
                }
                if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
                    return $_ENV[$key];
                }
                if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') {
                    return $_SERVER[$key];
                }
                return $default;
            };

            $host = $get('DB_HOST', 'localhost');
            $port = $get('DB_PORT', '3307');
            $dbName = $get('DB_NAME', 'questionnaire_app');
            $username = $get('DB_USER', 'root');
            $password = $get('DB_PASS', '');

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