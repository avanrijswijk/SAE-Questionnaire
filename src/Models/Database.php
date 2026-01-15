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

            // Try to read connection info from environment first (may be loaded by bootstrap)
            $appEnv = $get('APP_ENV', 'production');

            $host = $get('DB_HOST', null);
            $port = $get('DB_PORT', null);
            $dbName = $get('DB_NAME', null);
            $username = $get('DB_USER', null);
            $password = $get('DB_PASS', null);

            // If some essential variables are missing, attempt to load a .env file from project root
            if (empty($host) || empty($username) || empty($dbName)) {
                $possibleRoot = realpath(__DIR__ . '/../../');
                if ($possibleRoot && file_exists($possibleRoot . DIRECTORY_SEPARATOR . '.env') && class_exists('Dotenv\\Dotenv')) {
                    try {
                        \Dotenv\Dotenv::createImmutable($possibleRoot)->safeLoad();
                        $host = $get('DB_HOST', $host);
                        $port = $get('DB_PORT', $port);
                        $dbName = $get('DB_NAME', $dbName);
                        $username = $get('DB_USER', $username);
                        $password = $get('DB_PASS', $password);
                    } catch (\Throwable $e) {
                        // ignore and continue
                    }
                }
            }

            // Determine sensible defaults only for local/dev environment.
            $serverName = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : null;
            $localServers = array('127.0.0.1', '::1', 'localhost');
            if ($appEnv === 'local' || in_array($serverName, $localServers, true)) {
                $host = $host ?? '127.0.0.1';
                $port = $port ?? '3306';
                $dbName = $dbName ?? 'questionnaire_app';
                $username = $username ?? 'root';
                $password = $password ?? '';
            } else {
                // In production, require explicit DB settings to avoid accidentally using local root.
                if (empty($host) || empty($dbName) || empty($username)) {
                    throw new \RuntimeException('Missing database connection settings in environment. Define DB_HOST, DB_PORT, DB_NAME, DB_USER and DB_PASS for production.');
                }
                $port = $port ?? '3306';
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