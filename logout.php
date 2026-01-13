<?php
session_start();

// On détruit la session locale (tes variables $_SESSION sont effacées)
$_SESSION = array();
session_destroy();

// Configuration pour le retour au menu
// Détermine si on est en local ou sur le serveur
$whitelist_local = array('127.0.0.1', '::1', 'localhost');
$is_local = in_array($_SERVER['SERVER_NAME'], $whitelist_local);

// URL de base de ton site pour revenir dessus après déconnexion
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
$base_url = $protocol . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);

if ($is_local) {
    // En local, on redirige juste vers l'accueil
    header("Location: index.php");
    exit();
} else {
    // Sur le serveur, on envoie l'utilisateur se déconnecter du CAS
    // service = L'URL où le CAS doit nous renvoyer après déconnexion
    $cas_logout_url = "https://cas.unilim.fr/cas/logout?service=" . urlencode($base_url . '/index.php');
    
    header("Location: $cas_logout_url");
    exit();
}
?>