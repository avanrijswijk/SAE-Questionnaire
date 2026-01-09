<?php
require_once 'vendor/autoload.php';
require_once 'config.php';

phpCAS::client(CAS_VERSION, CAS_HOST, CAS_PORT, CAS_CONTEXT, true);
// Après la déconnexion CAS, on redirige l'utilisateur vers la racine du site
phpCAS::logoutWithRedirectService(MY_APP_URL);
?>