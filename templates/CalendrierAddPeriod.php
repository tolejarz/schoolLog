	<form action="" method="post">
		<div class="selectionBox">
			<p class="headerSelectionBox">Veuillez saisir les informations relatives à la période.</p>
				<table class="searchContainer">
					<tr>
						<?php if (empty($parms['id_classe'])) : ?>
						<th>Classe :</th>
						<td>
							<select name="id_classe">
							<?php
							foreach ($parms['classes'] as $class) {
								echo('<option ' . (@$_POST['id_classe'] == $class['id'] ? 'selected="selected" ' : '') . 'value="' . $class['id'] . '">' . $class['libelle'] . '</option>');
							}
							?>
							</select>
						</td>
						<?php endif; ?>
					</tr>
					<tr>
						<th>Type de période :</th>
						<td>
							<select id="type_periode" name="type">
								<option <?php echo(@$_POST['type'] == 'cours' ? 'selected="selected" ' : ''); ?>value="cours">cours</option>
								<option <?php echo(@$_POST['type'] == 'vacances' ? 'selected="selected" ' : ''); ?>value="vacances">vacances</option>
								<option <?php echo(@$_POST['type'] == 'partiels' ? 'selected="selected" ' : ''); ?>value="partiels">partiels</option>
							</select>
						</td>
					</tr>
					<tr>
						<th>Dates :</th>
						<td>
							du <input class="date" name="date_debut" type="text"<?php echo(isset($_POST['date_debut']) ? ' value="' . $_POST['date_debut'] . '"' : ''); ?> /><span class="thinSeparator">au</span>
							<input class="date" name="date_fin" type="text"<?php echo(isset($_POST['date_fin']) ? ' value="' . $_POST['date_fin'] . '"' : ''); ?> />
						</td>
					</tr>
				</table>

<?php 
$days = array('lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi');

echo('
	<select class="subject_id" style="display: none;">');
foreach ($parms['matieres'] as $matiere) {
	echo('<option value="' . $matiere['id'] . '">' . $matiere['nom'] . '</option>');
}
echo('
	</select>
	<a class="addLink create" href="#">Nouveau cours</a>
	<p class="separator4"></p>
	<div class="calendarContainer" style="width: 730px; margin: 0 auto; position: relative;">
		<div class="calendarWrapper" style="height: ' . (PART_HEIGHT * 26) . 'px; left: 61px; width: ' . ((SUBJECT_WIDTH + 7) * 5) . 'px;"></div>
		<table cellpadding="0" class="calendar">
			<tr>
				<th></th>');
$i = 0;
foreach ($days as $day) {
	echo('<th class="day">' . ucfirst($day) . '</th>');
}
echo('</tr>');
for ($h = 8; $h <= 20; $h++) {
	$start = $h == 8 ? 30 : 0;
	for ($m = $start; $m <= 30; $m += 30) {
		echo('<tr><th class="' . ($m == 30 ? 'hourMiddle' : 'hourStart') . '" style="width: 60px;"><p>' . ($m != 0 ? sprintf('%02d:%02d', $h, $m) : '') . '</p></th>');
		foreach ($days as $day) {
			echo('<td class="' . ($m == 0 ? 'hourStartCell' : ($h == 20 ? 'finalHourMiddleCell' : 'hourMiddleCell')) . '"></td>');
		}
		echo('</tr>');
	}
}
echo('
		</table>
		<p class="separator3"></p>
	</div>');
?>	
		<p class="submitContainer">
			<input name="validation" type="submit" value="Ajouter" />
			<input name="annulation" type="submit" value="Annuler" />
		</p>
	</form>
	</div>
