<?php
echo('
	<div class="selectionBox">
		<form action="" method="post">
			<p class="headerSelectionBox">
				Veuillez choisir la classe dont vous voulez afficher les matières :
				<select name="id_classe">');
foreach ($parms['classes'] as $c) {
	echo('<option ' . ($parms['id_classe'] == $c['id'] ? 'selected="selected" ' : '') . 'value="' . $c['id'] . '">' . $c['libelle'] . '</option>');
}
echo('
				</select>
			</p>
			<p class="submitContainer"><input type="submit" value="Valider" /></p>
		</form>
	</div>
	<p class="separator2"></p>');
?>
