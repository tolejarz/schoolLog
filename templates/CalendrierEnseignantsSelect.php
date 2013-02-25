<?php
echo('
	<div class="selectionBox">
		<form action="index.php?page=emploi_du_temps&amp;action=voir_enseignants" method="post">
			<p class="headerSelectionBoxThinner">Veuillez choisir l\'enseignant dont vous voulez afficher l\'emploi du temps :
				<select id="autosubmit_select" name="id_enseignant">');
foreach ($parms['enseignants'] as $e) {
echo('<option ' . ($parms['id_enseignant'] == $e['id'] ? 'selected="selected" ' : '') . 'value="' . $e['id'] . '">' . $e['civility'] . ' ' . $e['nom'] . '</option>');
}
echo('
				</select
			</p>
			<noscript><p class="submitContainer"><input type="submit" value="Valider" /></p></noscript>
		</form>
	</div>
	<p class="separator2"></p>');
?>
