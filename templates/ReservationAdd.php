<?php
echo('
	<div class="selectionBox">
		<form method="post" action="index.php?page=reservations&amp;action=add&amp;id_materiel=' . $parms['id_materiel'] . '">');
if ($_SESSION['user_privileges'] == 'enseignant') {
	echo('<input name="id_enseignant" type="hidden" value="' . $_SESSION['user_id'] . '" />
		<p class="headerSelectionBox">Veuillez saisir les informations relatives à votre réservation.</p>');
} else {
	echo('<p class="headerSelectionBox">Veuillez saisir les informations relatives à la réservation.</p>');
}
echo('
			<table class="searchContainer">
				<tr>
					<th>Date :</th>
					<td><input class="date" name="date_reservation" type="text" value="' . @$_POST['date_reservation'] . '" /></td>
				</tr>
				<tr>
					<th>De :</th>
					<td>
						<select name="heure_debut_h">');
for ($h = 8; $h <= 19; $h++) {
	echo('<option ' . (@$_POST['heure_debut_h'] == $h ? 'selected="selected"' : '') . 'value="' . $h . '">' . sprintf('%02d', $h) . '</option>');
}
echo('
						</select> h 
						<select name="heure_debut_m">');
$sm = isset($_POST['heure_debut_m']) ? $_POST['heure_debut_m'] : 30;
for ($m = 0; $m <= 30; $m = $m + 30) {
	echo('<option ' . ($m == $sm ? 'selected="selected"' : '') . 'value="' . $m . '">' . sprintf('%02d', $m) . '</option>');
}
echo('
						</select> <span class="thinSeparator">à</span> 
						<select name="heure_fin_h">');
for ($h = 9; $h <= 20; $h++) {
	echo('<option ' . (@$_POST['heure_fin_h'] == $h ? 'selected="selected"' : '') . 'value="' . $h . '">' . sprintf('%02d', $h) . '</option>');
}
echo('
						</select> h 
						<select name="heure_fin_m">');
$sm = isset($_POST['heure_fin_m']) ? $_POST['heure_fin_m'] : 30;
for ($m = 0; $m <= 30; $m = $m + 30) {
	echo('<option ' . ($m == $sm ? 'selected="selected"' : '') . 'value="' . $m . '">' . sprintf('%02d', $m) . '</option>');
}
echo('
						</select>
					</td>
				</tr>');
if ($_SESSION['user_privileges'] == 'superviseur') {
	echo('
					<tr>
						<th>Enseignant :</th>
						<td>
							<select name="id_enseignant">
								<option value=""></option>');
	foreach ($parms['enseignants'] as $c) {
		echo('<option ' . ($c['id'] == $_POST['id_enseignant'] ? 'selected="selected" ' : '') . 'value="' . $c['id'] . '">' . $c['civility'] . ' ' . $c['nom'] . '</option>');
	}
	echo('
							</select>
						</td>
					</tr>');
}
echo('
				<tr>
					<th>Matériel :</th>
					<td>
						<select name="id_materiel">');

$materiel_selected = isset($_POST['id_materiel']) ? $_POST['id_materiel'] : $parms['id_materiel'];
foreach ($parms['materiels'] as $m) {
	echo('<option ' . ($m['id'] == $materiel_selected ? 'selected="selected" ' : '') . 'value="' . $m['id'] . '">' . $m['type'] . ' ' . $m['modele'] . '</option>');
}
echo('
						</select>
					</td>
				</tr>
			</table>
			<p class="submitContainer">
				<input name="validation" type="submit" value="Réserver" />
				<input name="annulation" type="submit" value="Annuler" />
			</p>
		</form>
	</div>');
?>