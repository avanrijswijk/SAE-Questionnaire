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
<div class="container">	
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
                    <button type="submit" class="button is-danger">Annuler</button>
                    <button type="submit" class="button is-primary">Soumettre</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

