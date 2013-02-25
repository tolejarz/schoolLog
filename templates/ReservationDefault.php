<?php defined('EPSI') or die(); ?>

<?php
$week = $parms['_week'] - 1;

// récupère le numéro de la semaine du 31 août de l'annéde la promo actuelle
$lastWeek = date('W', mktime(12, 0, 0, 8, 31, CURRENT_PROMOTION));

// si la semaine choisie est supérieure à celle du 31 août de l'année de la promo alors cette semaine est bien celle de l'année de la promo
$daysWeek = week_dates($week, $week >= $lastWeek ? CURRENT_PROMOTION : CURRENT_PROMOTION + 1);
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

$daysDates = week_dates($week, date('Y'));

$days = array('lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi');


echo('
	<h2><p class="calendarTitle">Semaine ' . sprintf('%02d', $parms['_week']) . ' du ' . date('d/m/Y', $daysWeek[0]) . ' au ' . date('d/m/Y', $daysWeek[4]) . '</p>Liste des réservations</h2>
	<p class="separator4"></p>
	<a class="addLink" href="?page=reservations&amp;action=add&amp;id_materiel=' . $parms['id_materiel'] . '" target="_self">Ajouter une réservation</a>
	<form action="index.php?page=reservations" method="post">
		Matériel :
		<select id="autosubmit_select" name="id_materiel">');
foreach ($parms['materiels'] as $m) {
	echo('<option ' . ($parms['id_materiel'] == $m['id'] ? 'selected="selected" ' : '') . 'value="' . $m['id'] . '">' . $m['type'] . ' ' . $m['modele'] . '</option>');
}
echo('
		</select>
	</form>
	<p class="separator3"></p>
	<a class="weekLink" href="?page=reservations' . $parms['_arg'] . '&week=' . $nextWeekNumber . '" title="Semaine du ' . date('d/m/Y', $nextWeekTimestamp) . '" target="_self">Semaine ' . $nextWeekNumber . ' ></a>
	<a class="weekLink" href="?page=reservations' . $parms['_arg'] . '&week=' . $prevWeekNumber . '" title="Semaine du ' . date('d/m/Y', $prevWeekTimestamp) . '" target="_self">< Semaine ' . $prevWeekNumber . '</a>
	<div class="eventConfirmed" style="height: 13px; float: left; margin: 0 5px 0 0; width: 35px;"></div><div class="floatLeft">Réservation validée</div>
	<div class="eventPending" style="height: 13px; float: left; margin: 0 5px 0 25px; width: 35px;"></div><div class="floatLeft" style="margin-right: 10px;">Réservation en attente</div>
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
			
			$id = $cssClass = $acceptRefuseLink = $deleteLink = '';
			
			// Attribution de la classe CSS selon s'il s'agit d'un cours reporté et de l'état de la demande
			if ($row['etat'] == 'en attente') {
				$cssClass = 'Pending';
				if ($_SESSION['user_privileges'] == 'superviseur') {
					if (intval($row['date_heure_debut']) > strtotime('now') && ($_SESSION['user_privileges'] == 'superviseur' || ($_SESSION['user_privileges'] == 'enseignant' && ($_SESSION['user_id'] == $row['id_enseignant'])))) {
						$acceptRefuseLink = '<a class="acceptRefuseReservationLink" rel="' . $row['id'] . '" href="#" title="Valider ou refuser la réservation"></a>';
					}
				}
			} else if ($row['etat'] == 'validée') {
				$cssClass = 'Confirmed';
			} else if ($row['etat'] == 'refusée') {
				$cssClass = 'Refused';
			}
			
			// On teste si la date de la réservation est passée ou non
			if (intval($row['date_heure_debut']) > strtotime('now') && ($_SESSION['user_privileges'] == 'superviseur' || ($_SESSION['user_privileges'] == 'enseignant' && ($_SESSION['user_id'] == $row['id_enseignant'])))) {
				$deleteLink = '<a class="deleteLink" href="?page=reservations&amp;action=delete&amp;id=' . $row['id'] . '&amp;id_materiel=' . $parms['id_materiel'] . '" title="Supprimer la réservation" target="_self"></a>';
			}
			
			echo('
				<div ' . $id . 'class="event' . $cssClass . '" style="position: absolute; height: ' . ($height - 3) . 'px; left: ' . $left . 'px; top: ' . $top . 'px; width: ' . SUBJECT_WIDTH . 'px;">
					' . $deleteLink . $acceptRefuseLink . '
					<p class="hourLabel">' . $row['heure_debut'] . ' - ' . $row['heure_fin'] . '</p>
					<p class="eventLabel">' . $row['enseignant'] . '</p>
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
	echo('<th class="day">' . ucfirst($day) . ' <p class="dateLabel">'. date('d/m', $daysDates[$i++]) . '</p></th>');
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
?>
