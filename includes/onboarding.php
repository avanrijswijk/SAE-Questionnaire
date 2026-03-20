<?php
// On vérifie si le cookie existe.
// NOTE : On vérifie aussi si on est sur la page de logout pour ne pas bloquer la sortie !
$consentement_ok = isset($_COOKIE['rgpd_consent']) && $_COOKIE['rgpd_consent'] === 'true';
$page_actuelle = basename($_SERVER['PHP_SELF']);

if (!$consentement_ok && $page_actuelle != 'logout.php'):
?>
    <style>
        /* On floute le site derrière */
        body > *:not(#onboarding-overlay) { filter: blur(8px); pointer-events: none; user-select: none; }
        
        #onboarding-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.85); z-index: 10000;
            display: flex; justify-content: center; align-items: center;
        }
        #onboarding-box {
            background: white; width: 90%; max-width: 550px;
            padding: 30px; border-radius: 12px; text-align: center;
            box-shadow: 0 0 50px rgba(0,0,0,0.5); font-family: sans-serif;
        }
        .btn-gros {
            background: #005b96; color: white; border: none; padding: 15px 30px;
            font-size: 1.1em; border-radius: 8px; cursor: pointer; margin-top: 20px;
            width: 100%; transition: 0.3s;
        }
        .btn-gros:hover { background: #004470; }
        .btn-refuser {
            background: transparent; border: none; color: #999; margin-top: 15px;
            cursor: pointer; text-decoration: underline;
        }
    </style>

    <div id="onboarding-overlay">
        <div id="onboarding-box">
            <h2>👋 Bienvenue, <?php echo htmlspecialchars($_SESSION['cas_prenom'] ?? 'Etudiant'); ?> !</h2>
            
            <p style="color:#666; line-height:1.5; margin: 20px 0;">
                Pour utiliser notre générateur de questionnaires, nous avons besoin de valider votre profil.
                En continuant, vous acceptez que votre <strong>Nom, Prénom et Identifiant</strong> 
                soient enregistrés dans notre base de données sécurisée pour gérer vos créations et réponses.
            </p>

            <button class="btn-gros" onclick="accepterEtContinuer()">J'accepte et je continue</button>
            <br>
            <button class="btn-refuser" onclick="window.location.href='logout.php'">Non merci, je me déconnecte</button>
        </div>
    </div>

    <script>
        function accepterEtContinuer() {
            // On change le texte du bouton pour montrer que ça charge
            const btn = document.querySelector('.btn-gros');
            btn.innerText = "Validation en cours...";
            btn.style.opacity = "0.7";

            // Appel AJAX vers notre fichier PHP de l'étape 2
            fetch('api_save_consent.php')
            .then(response => response.text())
            .then(data => {
                if(data.trim() === 'success') {
                    // Si tout est OK, on recharge la page
                    // Le PHP verra le cookie et n'affichera plus cette fenêtre
                    location.reload();
                } else {
                    alert("Erreur lors de l'enregistrement : " + data);
                }
            })
            .catch(error => {
                alert("Erreur réseau : " + error);
            });
        }
    </script>
    
    <?php exit(); ?>

<?php endif; ?>