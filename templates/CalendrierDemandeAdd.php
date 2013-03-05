<?php
echo('
	<div class="selectionBox">
		<form action="" method="post">
			<p class="headerSelectionBox">Veuillez saisir les informations relatives à votre demande de report de cours.</p>
			<table class="searchContainer">
				<tr>
					<th>Classe :</th>
					<td><select name="id_classe">');
foreach ($parms['classes'] as $c) {
	echo('<option ' . ($c['id'] == @$_POST['id_classe'] ? 'selected="selected" ' : '') . 'value="' . $c['id'] . '">' . $c['libelle'] . '</option>');
}
echo('
					</td>
				</tr>
				<tr>
					<th>Date d\'origine :</th> 
					<td>
						<input class="date" name="date_origine" type="text" value="' . @$_POST['date_origine'] . '" /> <span class="thinSeparator">à</span> 
						<select name="heure_origine_h">');
for ($h = 8; $h <= 19; $h++) {
	echo('<option ' . (@$_POST['heure_origine_h'] == $h ? 'selected="selected"' : '') . 'value="' . $h . '">' . sprintf('%02d', $h) . '</option>');
}
echo('
						</select> h 
						<select name="heure_origine_m">');
$sm = isset($_POST['heure_origine_m']) ? $_POST['heure_origine_m'] : 30;
for ($m = 0; $m <= 30; $m = $m + 30) {
	echo('<option ' . ($m == $sm ? 'selected="selected" ' : '') . 'value="' . $m . '">' . sprintf('%02d', $m) . '</option>');
}
echo('
						</select>
					</td>
				</tr>
				<tr>
					<th class="top">Date de report :</th>
					<td>
						<p class="thinParagraph"><input ' . (!isset($_POST['hasDateReport']) || ($_POST['hasDateReport'] == 'no') ? 'checked="checked" ' : '') . 'id="hasDateReportNo" name="hasDateReport" type="radio" value="no" /><label for="hasDateReportNo">Ne pas proposer de date de report</label></p>
						<p class="thinParagraph"><input ' . (@$_POST['hasDateReport'] == 'yes' ? 'checked="checked" ' : '') . 'id="hasDateReportYes" name="hasDateReport" type="radio" value="yes" /><label for="hasDateReportYes">Proposer une date de report</label></p>
						<p class="indentedParagraph">
						<input class="date" name="date_report" type="text" value="' . @$_POST['date_report'] . '" /> <span class="thinSeparator">à</span> 
						<select name="heure_report_h">');
for ($h = 8; $h <= 20; $h++) {
	echo('<option ' . (@$_POST['heure_report_h'] == $h ? 'selected="selected" ' : '') . 'value="' . $h . '">' . sprintf('%02d', $h) . '</option>');
}
echo('
						</select> h 
						<select name="heure_report_m">');
$sm = isset($_POST['heure_report_m']) ? $_POST['heure_report_m'] : 30;
for ($m = 0; $m <= 30; $m = $m + 30) {
	echo('<option ' . ($m == $sm ? 'selected="selected" ' : '') . 'value="' . $m . '">' . sprintf('%02d', $m) . '</option>');
}
echo('
						</select>
						</p>
					</td>
				</tr>
			</table>
			<p class="submitContainer">
				<input name="validation" type="submit" value="Valider" />
				<input name="annulation" type="submit" value="Annuler" />
			</p>
		</form>
	</div>');
?>
