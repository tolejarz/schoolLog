<?php
echo('
	<div class="selectionBox">
		<form action="" method="post">
			<p class="headerSelectionBoxThinner">
				Veuillez choisir la classe dont vous voulez afficher l\'emploi du temps :
				<select id="autosubmit_select" name="id_classe">');
foreach ($parms['classes'] as $c) {
echo('<option ' . ($parms['id_classe'] == $c['id'] ? 'selected="selected" ' : '') . 'value="' . $c['id'] . '">' . $c['libelle'] . '</option>');
}
echo('
				</select>
			</p>
			<noscript><p class="submitContainer"><input type="submit" value="Afficher" /></p></noscript>
		</form>
	</div>
	<p class="separator2"></p>');
?>
