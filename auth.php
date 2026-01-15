<?php
require_once 'vendor/autoload.php';
require_once 'config.php';

// Initialisation du client CAS
phpCAS::client(CAS_VERSION, CAS_HOST, CAS_PORT, CAS_CONTEXT, true);

// Permet à votre PHP d'accepter la réponse du serveur CAS sans validation SSL
phpCAS::setNoCasServerValidation();

// Forcer la connexion
phpCAS::forceAuthentication();

// Authentification réussie, récupérer le nom d'utilisateur
$user = phpCAS::getUser();
?>