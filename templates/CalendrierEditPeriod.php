<form action="" method="post">
	<div class="selectionBox">
		<p class="headerSelectionBox">Veuillez saisir les informations relatives à la période.</p>
		<table class="searchContainer">
			<tr>
				<th>Type de période :</th>
				<td>
					<?php
					$type = isset($_POST['type']) ? $_POST['type'] : $parms['type'];
					
					?>
					<select id="type_periode" name="type">
						<option <?php echo($type == 'cours' ? 'selected="selected" ' : ''); ?>value="cours">cours</option>
						<option <?php echo($type == 'vacances' ? 'selected="selected" ' : ''); ?>value="vacances">vacances</option>
						<option <?php echo($type == 'partiels' ? 'selected="selected" ' : ''); ?>value="partiels">partiels</option>
					</select>
				</td>
			</tr>
			<tr>
				<th>Dates :</th>
				<td>
					du <input class="date" name="date_debut" type="text" value="<?php echo(isset($_POST['date_debut']) ? $_POST['date_debut'] : $parms['date_debut']); ?>" /><span class="thinSeparator">au</span>
					<input class="date" name="date_fin" type="text" value="<?php echo(isset($_POST['date_fin']) ? $_POST['date_fin'] : $parms['date_fin']); ?>" />
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
	<p class="separator4"></p>');
	
echo('
	<select class="subject_id" style="display: none;">');
foreach ($parms['matieres'] as $matiere) {
	echo('<option value="' . $matiere['id'] . '">' . $matiere['nom'] . '</option>');
}
echo('
	</select>
	<div class="calendarContainer" style="width: 730px; margin: 0 auto; position: relative;">
		<div class="calendarWrapper" style="height: ' . (PART_HEIGHT * 26) . 'px; left: 61px; width: ' . ((SUBJECT_WIDTH + 7) * 5) . 'px;">');

$i = 0;
foreach ($parms['cours'] as $cours) {
	if (in_array($cours['jour_libelle'], $days)) {
		$heure_debut_h = substr($cours['heure_debut'], 0, 2);
		$heure_debut_m = substr($cours['heure_debut'], 3, 2);
		$heure_fin_h = substr($cours['heure_fin'], 0, 2);
		$heure_fin_m = substr($cours['heure_fin'], 3, 2);
		
		// Calcul du left du cours
		$left = intval(array_search($cours['jour_libelle'], $days)) * (SUBJECT_WIDTH + 7) + 1;
		
		// Calcul du top du cours
		$nbDemiesHeuresDebut = ($heure_debut_h  * 60 + $heure_debut_m) - (8 * 60 + 30);
		$top = ($nbDemiesHeuresDebut / 30) * (PART_HEIGHT + 1);
		
		// Calcul du height du cours
		$duree = (($heure_fin_h * 60 + $heure_fin_m) - ($heure_debut_h  * 60 + $heure_debut_m)) / 30;
		$height = $duree * (PART_HEIGHT + 1) - 4;
		
		
		
		// Attribution de l'ID qui conditionne le drag 'n drop (le superviseur peut TOUJOURS déplacer un cours. L'enseignant du cours concerné ne peut le déplacer que tant que la demande n'est pas validée)
		$id = ($cours['jour'] - 1) . '-' . $cours['heure_debut'] . '-' . $cours['heure_fin'] . '-' . $cours['id_matiere'] . '-' . $cours['id'];
		echo('
			<div class="event draggable" style="position: absolute; height: ' . ($height - 3) . 'px; left: ' . $left . 'px; top: ' . $top . 'px; width: ' . SUBJECT_WIDTH . 'px;">
				<a class="deleteLink deleteCours" href="#"></a>
				<p class="hourLabel">' . $cours['heure_debut'] . ' - ' . $cours['heure_fin'] . '</p>
				<select class="subject_id">');
		foreach ($parms['matieres'] as $matiere) {
			echo('<option ' . ($matiere['id'] == $cours['id_matiere'] ? 'selected="selected" ' : '') . 'value="' . $matiere['id'] . '">' . $matiere['nom'] . '</option>');
		}
		echo('</select>
				<input name="event' . $i++ . '" type="hidden" value="' . $id . '" />
			</div>');
	}
}

echo('
		</div>
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
			<input name="validation" type="submit" value="Valider" />
			<input name="annulation" type="submit" value="Annuler" />
		</p>
	</form>
</div>
