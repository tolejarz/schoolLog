<?php
echo('
	<div class="selectionBox">
		<form action="" method="post">
		<p class="headerSelectionBox">Veuillez choisir la matière à ajouter à la classe <b>' . $parms['classe'] . '</b>.</p>
		<table class="searchContainer">
			<tr>
				<th>Nom :</th>
				<td>
					<select name="id_matiere">');
foreach ($parms['subjects'] as $m) {
echo('<option value="' . $m['id'] . '">' . $m['nom'] . '</option>');
}
echo('
					</select>
				</td>
			</tr>
			<tr>
				<th class="top">Enseignants :</th>
				<td>
					<table class="condensedList">');
$i = 0;
foreach ($parms['enseignants'] as $e) {
if ($i % 2 == 0) {
	echo('<tr>');
}
echo('<td><input name="e_' . $e['id'] . '" type="checkbox" />' . $e['civility'] . ' ' . $e['nom'] . '</td>');
$i++;
if ($i % 2 == 0) {
	echo('</tr>');
}
}
if ($i % 2 == 1) {
echo('</tr>');
}
echo('
					</table>
				</td>
			</tr>
		</table>
		<p class="submitContainer">
			<input name="validation" type="submit" value="Ajouter" />
			<input name="annulation" type="submit" value="Annuler" />
		</p>
	</form>
</div>');
?>
