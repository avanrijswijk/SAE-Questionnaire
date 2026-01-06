<?php
if (!isset($questionnaire)) {
	echo '<p>Questionnaire non fourni.</p>';
	return;
}

$titre = isset($questionnaire['titre']) ? htmlspecialchars($questionnaire['titre']) : 'Sans titre';
$date_exp = isset($questionnaire['date_expiration']) ? htmlspecialchars($questionnaire['date_expiration']) : '';
$code = isset($questionnaire['code']) ? htmlspecialchars($questionnaire['code']) : '';
$auteur = isset($questionnaire['id_createur']) ? htmlspecialchars($questionnaire['id_createur']) : '';

?>
<div class="container" style="margin-top: 25px;">	
	<?php if ($date_exp): ?><p><strong>Expiration :</strong> <?php echo $date_exp; ?></p><?php endif; ?>
	<?php if ($code): ?><p><strong>Code :</strong> <?php echo $code; ?></p><?php endif; ?>
    <?php if ($auteur): ?><p><strong>Auteur :</strong> <?php echo $auteur; ?></p><?php endif; ?>
    
    <div class="questionnaire-container" style="justify-items: center;">
        <h1 class="title is-1 mt-3"><?php echo $titre; ?></h1>

        <?php if (empty($questions)): ?>
            <p>Aucune question pour ce questionnaire.</p>
        <?php else: ?>
            <form method="post" action="">
                <input type="hidden" name="id_questionnaire" value="<?php echo htmlspecialchars($questionnaire['id']); ?>">
                <?php foreach ($questions as $index => $q): ?>
                    <?php
                        $intitule = isset($q['intitule']) ? htmlspecialchars($q['intitule']) : 'Question sans texte';
                        $type = isset($q['id_type']) ? (int)$q['id_type'] : 1;
                        $required = (isset($q['est_obligatoire']) && $q['est_obligatoire']) ? 'required' : '';
                        $name = 'question_'.(isset($q['id']) ? $q['id'] : $index);
                    ?>
                    <hr />
                    <div class="question-block" style="padding: 25px 0;">
                        <label class="subtitle is-4"><strong><?php echo ($index+1).'. '; ?></strong><?php echo $intitule; ?> <?php if ($required) echo '<span style="color:red">*</span>'; ?></label>
                        <div class="answer">
                            <?php if ($type === 1): // texte ?>
                                <textarea name="<?php echo $name; ?>" <?php echo $required; ?> class="textarea" placeholder="Remplir ce champ..." cols="50"></textarea>
                            <?php elseif ($type === 2): // choix unique (radio) ?>
                                <p><em>Choix unique - options non disponibles dans la vue.</em></p>
                            <?php elseif ($type === 3): // choix multiple ?>
                                <p><em>Choix multiple - options non disponibles dans la vue.</em></p>
                            <?php else: ?>
                                <input type="text" name="<?php echo $name; ?>" <?php echo $required; ?> class="form-control" />
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <hr />

                <div class="buttons" style="display: flex; justify-content: center; margin-bottom: 50px;">

                    <button type="button" id="cancelBtn" class="button is-danger">Annuler</button>
                    <button type="submit" id="submitBtn" class="button is-primary">Soumettre</button>

                </div>

            </form>

            <!-- Modales de confirmation -->
            <style>
            .modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,0.5);display:none;align-items:center;justify-content:center;z-index:1000}
            .modal-card{background:#fff;padding:20px;border-radius:6px;max-width:520px;width:90%;box-shadow:0 8px 24px rgba(0,0,0,.2)}
            .modal-actions{display:flex;justify-content:flex-end;gap:10px;margin-top:16px}
            .modal-title{font-weight:700;margin-bottom:8px}
            </style>

            <div id="cancelModal" class="modal-overlay" role="dialog" aria-modal="true" aria-hidden="true">
                <div class="modal-card">
                    <div class="modal-content">
                        <div class="modal-title">Quitter la page ?</div>
                        <div class="modal-body">Êtes-vous sûr de vouloir quitter la page ? Les informations saisies ne pourront pas être enregistrées.</div>
                        <div class="modal-actions">
                            <button type="button" id="cancelModalNo" class="button">Annuler</button>
                            <button type="button" id="cancelModalYes" class="button is-danger">Quitter</button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="submitModal" class="modal-overlay" role="dialog" aria-modal="true" aria-hidden="true">
                <div class="modal-card">
                    <div class="modal-content">
                        <div class="modal-title">Confirmer l'envoi</div>
                        <div class="modal-body">Êtes-vous sûr de vouloir envoyer les informations ? Elles ne pourront plus être modifiées après envoi.</div>
                        <div class="modal-actions">
                            <button type="button" id="submitModalNo" class="button">Annuler</button>
                            <button type="button" id="submitModalYes" class="button is-primary">Envoyer</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Script JS pour la gestion des modales -->
            <script>
            (function(){
                const cancelBtn = document.getElementById('cancelBtn');
                const submitBtn = document.getElementById('submitBtn');
                const form = document.querySelector('form');

                const cancelModal = document.getElementById('cancelModal');
                const cancelNo = document.getElementById('cancelModalNo');
                const cancelYes = document.getElementById('cancelModalYes');

                const submitModal = document.getElementById('submitModal');
                const submitNo = document.getElementById('submitModalNo');
                const submitYes = document.getElementById('submitModalYes');

                function show(modal){ modal.style.display='flex'; modal.setAttribute('aria-hidden','false'); }
                function hide(modal){ modal.style.display='none'; modal.setAttribute('aria-hidden','true'); }

                if(cancelBtn){
                    cancelBtn.addEventListener('click', function(e){ e.preventDefault(); show(cancelModal); });
                }

                if(cancelNo){ cancelNo.addEventListener('click', function(){ hide(cancelModal); }); }
                if(cancelYes){ cancelYes.addEventListener('click', function(){ window.location.href='?c=home'; }); }

                if(submitBtn){
                    submitBtn.addEventListener('click', function(e){
                        // Empêcher l'envoi immédiat pour confirmation
                        e.preventDefault();
                        show(submitModal);
                    });
                }

                if(submitNo){ submitNo.addEventListener('click', function(){ hide(submitModal); }); }
                if(submitYes){ submitYes.addEventListener('click', function(){ hide(submitModal); if(form) form.submit(); }); }

                // Fermer modal sur clic en dehors de la carte
                [cancelModal, submitModal].forEach(modal=>{
                    modal.addEventListener('click', function(e){ if(e.target===modal) hide(modal); });
                });
            })();
            </script>

        <?php endif; ?>
    </div>
</div>

