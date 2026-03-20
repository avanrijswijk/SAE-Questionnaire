<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$prenom = isset($_SESSION['cas_prenom']) ? htmlspecialchars($_SESSION['cas_prenom'], ENT_QUOTES, 'UTF-8') : '';
$nom = isset($_SESSION['cas_nom']) ? htmlspecialchars($_SESSION['cas_nom'], ENT_QUOTES, 'UTF-8') : '';
$login = isset($_SESSION['cas_user']) ? htmlspecialchars($_SESSION['cas_user'], ENT_QUOTES, 'UTF-8') : '';
$email = isset($_SESSION['cas_email']) ? htmlspecialchars($_SESSION['cas_email'], ENT_QUOTES, 'UTF-8') : '';
// Récupère les groupes depuis la session (peut être array ou string)
$groupes = [];
if (isset($_SESSION['cas_groupes'])) {
  if (is_array($_SESSION['cas_groupes'])) {
    $groupes = $_SESSION['cas_groupes'];
  } elseif (is_string($_SESSION['cas_groupes'])) {
    $groupes = [$_SESSION['cas_groupes']];
  }
}
// S'assurer que chaque valeur est échappée
$groupes = array_map(function($groupe){ return htmlspecialchars($groupe, ENT_QUOTES, 'UTF-8'); }, $groupes);
?>

<section class="section">
  <div class="container">
    <h1 class="title">Profil</h1>
    <div class="box">
      <p><strong>Prénom :</strong> <?php echo $prenom; ?></p>
      <p><strong>Nom :</strong> <?php echo $nom; ?></p>
      <p><strong>Email :</strong> <?php echo $email ? $email : 'Non renseigné'; ?></p>
      </br>
      <p><strong>Login (Unilim) :</strong> <?php echo $login; ?></p>
      </br>
      <p><strong>Groupes :</strong>
        <?php if (empty($groupes)): ?>
          Aucun groupe
        <?php else: ?>
          <ul>
            <?php foreach ($groupes as $groupe): ?>
              <li><?php echo $groupe; ?></li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </p>
    </div>
    <a class="button is-link" href="./?c=home">Retour</a>
  </div>
</section>
