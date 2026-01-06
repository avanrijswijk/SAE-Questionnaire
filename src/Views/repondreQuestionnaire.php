<?php
if (!isset($questionnaire)) {
	echo '<p>Questionnaire non fourni.</p>';
	return;
}

$titre = isset($questionnaire['titre']) ? htmlspecialchars($questionnaire['titre']) : 'Sans titre';
$date_exp = isset($questionnaire['date_expiration']) ? htmlspecialchars($questionnaire['date_expiration']) : '';
$code = isset($questionnaire['code']) ? htmlspecialchars($questionnaire['code']) : '';

?>
<div class="container">
	<h1><?php echo $titre; ?></h1>
	<?php if ($date_exp): ?><p><strong>Expiration :</strong> <?php echo $date_exp; ?></p><?php endif; ?>
	<?php if ($code): ?><p><strong>Code :</strong> <?php echo $code; ?></p><?php endif; ?>

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
				<div class="question-block">
					<label><strong><?php echo ($index+1).'. '; ?></strong><?php echo $intitule; ?> <?php if ($required) echo '<span style="color:red">*</span>'; ?></label>
					<div class="answer">
						<?php if ($type === 1): // texte ?>
							<textarea name="<?php echo $name; ?>" <?php echo $required; ?> rows="3" class="form-control"></textarea>
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
			<button type="submit" class="btn btn-primary">Soumettre</button>
		</form>
	<?php endif; ?>
</div>

