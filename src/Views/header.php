<!DOCTYPE html>
<html lang="fr" data-theme="light">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page d'accueil</title>
    <link rel="icon" href="./src/Views/img/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.4/css/bulma.min.css">
    <link rel="stylesheet" href="./src/Views/css/style-header.css">
  </head>
  <body>
    <header id="header" style="box-shadow: rgba(17, 17, 26, 0.5) 0px 5px 10px;">
        <div id="div-header">
          <a href="./?c=home">
              <img id="img-accueil" 
               src="./src/Views/img/accueil.png" 
               alt="accueil">
          </a>

          <?php
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $prenom = '';
            if (!empty($_SESSION['cas_prenom'])) {
                $prenom = htmlspecialchars($_SESSION['cas_prenom'], ENT_QUOTES, 'UTF-8');
            } elseif (!empty($_SESSION['cas_user'])) {
                $prenom = htmlspecialchars($_SESSION['cas_user'], ENT_QUOTES, 'UTF-8');
            }
          ?>

          <div class="header-center">
            <div class="dropdown is-hoverable">
              <div class="dropdown-trigger">
                <button class="button" aria-haspopup="true" aria-controls="dropdown-menu">
                  <span>Bienvenue <?php echo $prenom ? $prenom : ''; ?></span>
                  <span class="caret">▼</span>
                </button>
              </div>
              <div class="dropdown-menu" id="dropdown-menu" role="menu">
                <div class="dropdown-content">
                  <a href="./?c=profil" class="dropdown-item">Profil</a>
                  <hr class="dropdown-divider">
                  <a href="./logout.php" class="dropdown-item">Déconnexion</a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <img id="img-header" 
            src="./src/Views/img/unilim.png" 
            alt="logo unilim"
            style="cursor: default;">
        
    </header>