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

            // Si les variables essentielles ne sont toujours pas définies, essayer
            // de charger .env depuis la racine du projet (utile pour certains SAPI).
            if (($host === 'localhost' && $get('DB_HOST', '') === '') || ($username === 'root' && $get('DB_USER', '') === '')) {
                $possibleRoot = realpath(__DIR__ . '/../../');
                if ($possibleRoot && file_exists($possibleRoot . DIRECTORY_SEPARATOR . '.env') && class_exists('Dotenv\\Dotenv')) {
                    try {
                        \Dotenv\Dotenv::createImmutable($possibleRoot)->safeLoad();
                        // repopulate variables from $_ENV/$_SERVER if available
                        $host = $get('DB_HOST', $host);
                        $port = $get('DB_PORT', $port);
                        $dbName = $get('DB_NAME', $dbName);
                        $username = $get('DB_USER', $username);
                        $password = $get('DB_PASS', $password);
                    } catch (\Throwable $e) {
                        // ignore and continue with defaults
                    }
                }
            }

            $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $dbName);

            try {
                $this->conn = new PDO($dsn, $username, $password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $exception) {
                error_log('Erreur de connexion DB: ' . $exception->getMessage());
                throw new \RuntimeException('Erreur de connexion à la base de données. Voir logs pour détails.');
            }
        }

        return $this->conn;
    }
}