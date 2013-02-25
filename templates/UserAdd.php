<?php
echo('
	<div class="selectionBox">
		<form action="index.php?page=utilisateurs&amp;action=add" method="post">
			<p class="headerSelectionBox">Veuillez saisir les informations relatives à l\'enseignant.</p>
			<table class="searchContainer">
				<tr>
					<th>Civilité :</th>
					<td>
						<select name="civility">
							<option ' . (@$_POST['civility'] == 'M.' ? 'selected="selected" ' : '') . 'value="M.">M.</option>
							<option ' . (@$_POST['civility'] == 'Mme' ? 'selected="selected" ' : '') . 'value="Mme">Mme</option>
							<option ' . (@$_POST['civility'] == 'Mlle' ? 'selected="selected" ' : '') . 'value="Mlle">Mlle</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>Nom :</th>
					<td><input name="nom" type="text" value="' . @$_POST['nom'] . '" /></td>
				</tr>
			</table>
			<table id="classesTable">');
define('NUM_COLS', 3);
$i = 0;
foreach ($parms['classes'] as $c) {
	if ($i % NUM_COLS == 0) {
		echo('<tr>');
	}
	$i++;
	echo('
					<td class="top" style="width: ' . ceil(100 / NUM_COLS) . '%"><p class="classesTableHeader">' . $c['libelle'] . '</p>');
	if (empty($c['matieres'])) {
		echo('<p class="notice">Aucune matière</p>');
	} else {
		foreach ($c['matieres'] as $m) {
			$inputId = $c['id'] . '_' . $m['id'];
			echo('<p class="thinParagraph"><input ' . (@$_POST['cm_' . $inputId] ? 'checked="checked" ' : '') . 'name="cm_' . $inputId . '"  type="checkbox" /> <label for="">' . $m['nom'] . '</label></p>');
		}
	}
	echo('</td>');
	if ($i % NUM_COLS == 0) {
		echo('</tr>');
	}
}
if ($i % NUM_COLS != 0) {
	echo('</tr>');
}
echo('
			</table>
			<p class="submitContainer">
				<input name="validation" type="submit" value="Ajouter" />
				<input name="annulation" type="submit" value="Annuler" />
			</p>
		</form>
	</div>');
?>
