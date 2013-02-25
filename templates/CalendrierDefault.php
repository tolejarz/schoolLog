<?php
$week = $parms['_week'] - 1;

// si la semaine choisie est supérieure à celle du 31 août de l'année de la promo alors cette semaine est bien celle de l'année de la promo
$daysWeek = week_dates($week, $week >= LAST_WEEK ? CURRENT_PROMOTION : CURRENT_PROMOTION + 1);
$firstDay = $daysWeek[0];

// semaine précédente
$prevWeekTimestamp = strtotime('-1 week', $firstDay);
$prevWeekNumber = intval(date('W', $prevWeekTimestamp));
$prevWeekYear = date('Y', $prevWeekTimestamp);
$prevWeekDate = week_dates($prevWeekNumber, $prevWeekYear);
$prevWeekDate = $prevWeekDate[0];

// semaine suivante
$nextWeekTimestamp = strtotime('+1 week', $firstDay);
$nextWeekNumber = intval(date('W', $nextWeekTimestamp));
$nextWeekYear = date('Y', $nextWeekTimestamp);
$nextWeekDate = week_dates($nextWeekNumber, $nextWeekYear);
$nextWeekDate = $nextWeekDate[0];

$days = array('lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi');


/*
//ANCIENNE PRESENTATION :
echo('
	<p class="floatLeft">
		<a class="weekLink" href="?page=emploi_du_temps' . $parms['_arg'] . '&week=' . $prevWeekNumber . '" target="_self">< Semaine ' . $prevWeekNumber . ' (' . date('d/m/Y', $prevWeekTimestamp) . ')</a>
	</p>
	<p class="floatRight">
		<a class="weekLink" href="?page=emploi_du_temps' . $parms['_arg'] . '&week=' . $nextWeekNumber . '" target="_self">Semaine ' . $nextWeekNumber . ' (' . date('d/m/Y', $nextWeekTimestamp) . ') ></a>
	</p>
	<p class="separator3"></p>');

echo('
	<div class="eventConfirmed" style="height: 13px; float: left; margin: 0 5px 0 0; width: 35px;"></div><div class="floatLeft">Cours déplacé</div>
	<div class="event" style="height: 13px; float: left; margin: 0 5px 0 20px; width: 35px;"></div><div class="floatLeft">Cours normal</div>' . 
	(in_array($_SESSION['user_privileges'], array('enseignant', 'superviseur')) ? '<div class="eventPending" style="height: 13px; float: left; margin: 0 5px 0 25px; width: 35px;"></div><div class="floatLeft" style="margin-right: 10px;">Report à confirmer</div>' : '') . '
	<p class="separator3"></p>');
*/


echo('
	<h2><p class="calendarTitle">Semaine ' . sprintf('%02d', $parms['_week']) . ' du ' . date('d/m/Y', $daysWeek[0]) . ' au ' . date('d/m/Y', $daysWeek[4]) . '</p>Emploi du temps</h2>
	<p class="separator3"></p>
	<a class="weekLink" href="?page=emploi_du_temps' . $parms['_arg'] . '&week=' . $nextWeekNumber . '" title="Semaine du ' . date('d/m/Y', $nextWeekTimestamp) . '" target="_self">Semaine ' . $nextWeekNumber . ' ></a>
	<a class="weekLink" href="?page=emploi_du_temps' . $parms['_arg'] . '&week=' . $prevWeekNumber . '" title="Semaine du ' . date('d/m/Y', $prevWeekTimestamp) . '" target="_self">< Semaine ' . $prevWeekNumber . '</a>
	<div class="eventConfirmed" style="height: 13px; float: left; margin: 0 5px 0 0; width: 35px;"></div><div class="floatLeft">Cours déplacé</div>
	<div class="event" style="height: 13px; float: left; margin: 0 5px 0 20px; width: 35px;"></div><div class="floatLeft">Cours normal</div>' . 
	(in_array($_SESSION['user_privileges'], array('enseignant', 'superviseur')) ? '<div class="eventPending" style="height: 13px; float: left; margin: 0 5px 0 25px; width: 35px;"></div><div class="floatLeft" style="margin-right: 10px;">Report à confirmer</div>' : '') . '
	<p class="separator3"></p>');

echo('
	<div class="calendarContainer" style="position: relative;">
		<div class="calendarWrapper" style="height: ' . (PART_HEIGHT * 26) . 'px; width: ' . ((SUBJECT_WIDTH + 7) * 5) . 'px;">');
foreach ($parms as $dayname => $cours) {
	if (in_array($dayname, $days)) {
		foreach ($cours as $row) {
			$heure_debut_h = substr($row['heure_debut'], 0, 2);
			$heure_debut_m = substr($row['heure_debut'], 3, 2);
			$heure_fin_h = substr($row['heure_fin'], 0, 2);
			$heure_fin_m = substr($row['heure_fin'], 3, 2);
			
			// Calcul du left du cours
			$left = intval(array_search($dayname, $days)) * (SUBJECT_WIDTH + 7) + 1;
			
			// Calcul du top du cours
			$nbDemiesHeuresDebut = ($heure_debut_h  * 60 + $heure_debut_m) - (8 * 60 + 30);
			$top = ($nbDemiesHeuresDebut / 30) * (PART_HEIGHT + 1);
			
			// Calcul du height du cours
			$duree = (($heure_fin_h * 60 + $heure_fin_m) - ($heure_debut_h  * 60 + $heure_debut_m)) / 30;
			$height = $duree * (PART_HEIGHT + 1) - 4;
			
			// Texte "(report du xx/xx/xxxx)"
			$report = '<p class="reportLabel">' . (isset($row['new']) ? '(report du ' . $row['new'] . ')' : (isset($row['old']) ? '(reporté au ' . $row['old'] . ')' : '')) . '</p>';
			
			$supportsLink = $id = $cssClass = $acceptRejectLink = $class = '';
			
			if (!$row['holidays']) {
				// Affichage de la classe si $parms['_displayClasses']
				if ($parms['_displayClasses']) {
					$class = '<p class="eventLabel">' . $row['classe'] . '</p>';
				}
			
				// Affichage du lien vers les supports
				if ($_SESSION['user_privileges'] == 'enseignant') {
					if ($row['id_enseignant'] == $_SESSION['user_id']) {
						$supportsLink = '<a class="supportsLink" href="?page=supports#' . $row['id_classe'] . '-' . $row['id_matiere'] . '" target="_self" title="Afficher les supports"></a>';
					}
				} else if ($_SESSION['user_privileges'] == 'eleve') {
					$supportsLink = '<a class="supportsLink" href="?page=supports&id_matiere=' . $row['id_matiere'] . '" target="_self" title="Afficher les supports"></a>';
				} else {
					$supportsLink = '<a class="supportsLink" href="?page=supports&id_classe=' . $row['id_classe'] . '&id_matiere=' . $row['id_matiere'] . '" target="_self" title="Afficher les supports"></a>';
				}
			
				// Attribution de l'ID qui conditionne le drag 'n drop (le superviseur peut TOUJOURS déplacer un cours. L'enseignant du cours concerné ne peut le déplacer que tant que la demande n'est pas validée)
				if ((($_SESSION['user_privileges'] == 'enseignant') && ($row['id_enseignant'] == $_SESSION['user_id'])) || ($_SESSION['user_privileges'] == 'superviseur')) {
					if (!$parms['_blockDnD'] && !(($row['id_enseignant'] == $_SESSION['user_id']) && ($row['etat'] == 'validée'))) {
						$id = 'id="' . $row['id'] . (isset($row['id_operation']) ? '-' . $row['id_operation'] : '') . ':' . $row['jour'] . ':' . implode('-', $daysWeek) . '" ';
					}
				}
			
				// Attribution de la classe CSS selon s'il s'agit d'un cours reporté et de l'état de la demande
				if (!empty($row['new'])) {
					if ($row['etat'] == 'en attente') {
						$cssClass = 'Pending';
						if ($_SESSION['user_privileges'] == 'superviseur') {
							$acceptRejectLink = '<a class="acceptRefuseReportLink" rel="' . $row['id_operation'] . '" href="#" title="Valider ou refuser le report"></a>';
						}
					} else if ($row['etat'] == 'validée') {
						$cssClass = 'Confirmed';
					} else if ($row['etat'] == 'refusée') {
						$cssClass = 'Refused';
					}
				}
			} else {
				$cssClass = 'Disabled';
			}
			
			echo('
				<div ' . $id . 'class="event' . $cssClass . '" style="position: absolute; height: ' . ($height - 3) . 'px; left: ' . $left . 'px; top: ' . $top . 'px; width: ' . SUBJECT_WIDTH . 'px;">
					' . $supportsLink . '
					' . $acceptRejectLink . '
					' . ($id != '' ? '<a class="moveableTip" title="Cliquez et glissez la souris pour déplacer ce cours"></a>' : '') . '
					' . ($row['holidays'] ? '' : '<p class="hourLabel">' . $row['heure_debut'] . ' - ' . $row['heure_fin'] . '</p>') . '
					' . $class . '
					' . ($row['holidays'] ? '<p class="holidaysLabel">Vacances</p>' : '<p class="eventLabel">' . $row['matiere'] . '</p>') . '
					' . $report . '
				</div>');
		}
	}
}
echo('</div>
	<table cellpadding="0" class="calendar">
			<tr>
				<th></th>');
$i = 0;
foreach ($days as $day) {
	echo('<th class="day">' . ucfirst($day) . ' <p class="dateLabel">'. date('d/m/y', $daysWeek[$i++]) . '</p></th>');
}
echo('</tr>');
for ($h = 8; $h <= 20; $h++) {
	$start = $h == 8 ? 30 : 0;
	for ($m = $start; $m <= 30; $m += 30) {
		echo('<tr><th class="' . ($m == 30 ? 'hourMiddle' : 'hourStart') . '"><p>' . ($m != 0 ? sprintf('%02d:%02d', $h, $m) : '') . '</p></th>');
		foreach ($parms as $dayname => $cours) {
			if (in_array($dayname, $days)) {
				echo('<td class="' . ($m == 0 ? 'hourStartCell' : ($h == 20 ? 'finalHourMiddleCell' : 'hourMiddleCell')) . '"></td>');
			}
		}
		echo('</tr>');
	}
}
echo('
		</table>
	</div>
	<div id="dialog"></div>');


/* 
$days = array('lundi' => 0, 'mardi' => 0, 'mercredi' => 0, 'jeudi' => 0, 'vendredi' => 0);
echo('
	<div class="calendarContainer">
	<table cellpadding="0" class="calendar">
		<tr><th></th>');
$i = 0;
foreach ($days as $day => $val) {
	echo('<th class="day">' . ucfirst($day) . ' <p class="dateLabel">'. date('d/m', $daysDates[$i++]) . '</p></th>');
}
echo('</tr>');

for ($h = 8; $h <= 20; $h++) {
	$start = $h == 8 ? 30 : 0;
	for ($m = $start; $m <= 30; $m += 30) {
		foreach ($days as $day => $val) {
			if ($val > 0) {
				$days[$day]--;
			}
		}
		echo('<tr><th class="' . ($m == 30 ? 'hourMiddle' : 'hourStart') . '"><p>' . ($m != 0 ? sprintf('%02d:%02d', $h, $m) : '') . '</p></th>');
		foreach ($parms as $dayname => $cours) {
			if (in_array($dayname, array_keys($days))) {
				$hasClass = false;
				foreach ($cours as $row) {
					if ( ($days[$dayname] == 0) && ($row['heure_debut'] == sprintf('%02d:%02d', $h, $m)) ) {
						$hasClass = true;
						$fin = explode(':', $row['heure_fin']);
						$days[$dayname] = (($fin[0] * 60 + $fin[1]) - ($h * 60 + $m)) / 30;
						break;
					}
				}
				if ($hasClass) {
					$extra = (isset($row['new']) ? '<p class="extraLabel">(report du ' . $row['new'] . ')</p>' : (isset($row['old']) ? '<p class="extraLabel">(reporté au ' . $row['old'] . ')</p>' : ''));
					echo('
						<td rowspan="' . $days[$dayname] . '" style="height: ' . (22 * $days[$dayname] - 2) . 'px">
							<div style="height: ' . (22 * $days[$dayname] - 7) . 'px"><div class="subject' . (isset($row['new']) ? 'New' : (isset($row['old']) ? 'Old' : '')) . '"><p class="hourLabel">' . $row['heure_debut'] . ' - ' . $row['heure_fin'] . '</p><p class="subjectLabel">' . $row['matiere'] . '</p>');
							if ((in_array('enseignant', $_SESSION['user_privileges']) && empty($_GET['action'])) || (in_array('superviseur', $_SESSION['user_privileges']) && $_GET['action']=="voir_enseignants")) echo('<p class="subjectLabel">' . $row['classe'] . '</p>');
							echo('' . $extra . '</div></div>
						</td>');
				} else if ($days[$dayname] == 0) {
					echo('<td class="' . ($m == 0 ? 'hourStartCell' : 'hourMiddleCell') . '"></td>');
				}
			}
		}
		echo('</tr>');
	}
}
echo('</table></div>');
*/
?>
