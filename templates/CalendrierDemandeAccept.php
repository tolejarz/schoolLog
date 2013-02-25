<?php
echo('
	<div class="selectionBox">
		<form action="index.php?page=emploi_du_temps&amp;action=acceptdemande&amp;id=' . $parms['id'] . '" method="post">
			<p class="headerSelectionBox">Etes-vous sûr de vouloir accepter cette demande ?</p>
			<table class="searchContainer">
				<tr>
					<th>Classe :</th>
					<td>' . $parms['classe'] . '</td>
				</tr>
				<tr>
					<th>Enseignant :</th>
					<td>' . $parms['enseignant'] . '</td>
				</tr>
				<tr>
					<th>Matière :</th>
					<td>' . $parms['matiere'] . '</td>
				</tr>
				<tr>
					<th>Date d\'origine :</th>
					<td>' . $parms['date_origine'] . ' à ' . $parms['heure_origine'] . '</td>
				</tr>
				<tr>
					<th>Date de report :</th>
					<td>
						<input class="date" name="date_report" type="text" value="' . $parms['date_report'] .'" /> <span class="thinSeparator">à</span> 
						<select name="heure_report_h">');
$sh = isset($_POST['heure_report_h']) ? $_POST['heure_report_h'] : substr($parms['heure_report'], 0, 2);
for ($h = 8; $h <= 19; $h++) {
	echo('<option ' . ($h == $sh ? 'selected="selected"' : '') . 'value="' . $h . '">' . sprintf('%02d', $h) . '</option>');
}
echo('
						</select> h 
						<select name="heure_report_m">');
$sm = isset($_POST['heure_report_m']) ? $_POST['heure_report_m'] : substr($parms['heure_report'], 3, 2);
for ($m = 0; $m <= 30; $m = $m + 30) {
	echo('<option ' . ($m == $sm ? 'selected="selected"' : '') . 'value="' . $m . '">' . sprintf('%02d', $m) . '</option>');
}
echo('
						</select>
					</td>
				</tr>
			</table>
			<p class="submitContainer">
				<input name="validation" type="submit" value="Accepter" />
				<input name="annulation" type="submit" value="Annuler" />
			</p>
		</form>
	</div>');
?>
