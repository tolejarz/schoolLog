<?php
$css = array('' => '', 'en attente' => 'pending', 'validée' => 'accepted', 'refusée' => 'refused');
echo('
	<h2><p class="calendarTitle">'. ucfirst(getFrMonth($parms['month'])) . ' ' . $parms['year'] . '</p>Historique des demandes de report</h2>
	<p class="floatLeft">
		<a class="monthLink" href="' . Router::build('CalendarRequestHistory', NULL, array('month' => $parms['prevMonth'], 'year' => $parms['prevYear'])) . '" target="_self">< ' .  ucfirst(getFrMonth($parms['prevMonth'])) . ' ' . $parms['prevYear'] . '</a>
	</p>');
if ($parms['nextYear'] . $parms['nextMonth'] <= date('Ym')) {
	echo('
		<p class="floatRight">
			<a class="monthLink" href="' . Router::build('CalendarRequestHistory', NULL, array('month' => $parms['nextMonth'], 'year' => $parms['nextYear'])) . '" target="_self">' .  ucfirst(getFrMonth($parms['nextMonth'])) . ' ' . $parms['nextYear'] . ' ></a>
		</p>');
}
	echo('<p class="separator2"></p>');

if (!empty($parms['demandes'])) {
	echo('
		<table class="listingContainer">
			<tr>
				<th>Classe</th>
				' . ($_SESSION['user_privileges'] == 'superviseur' ? '<th>Enseignant</th>' : '') . '
				<th>Matière</th>
				<th>Date/Heure d\'origine</th>
				<th>Date/Heure de report</th>
				<th>Etat</th>
			</tr>');
	foreach ($parms['demandes'] as $d) {
		echo('<tr>
				<td class="centered">' . $d['classe'] . '</td>
				' . ($_SESSION['user_privileges'] == 'superviseur' ? '<td class="centered">' . $d['enseignant'] . '</td>' : '') . '
				<td class="centered">' . $d['matiere'] . '</td>
				<td class="centered">' . $d['date_origine'] . ' à ' . $d['heure_origine'] . '</td>
				<td class="centered">' . ($d['date_report'] != null ? $d['date_report'] .' à  ' . $d['heure_report']  : '(non définie)') . '</td>
				<td class="' . $css[$d['etat']] . '">' . $d['etat'] . '</td>
			</tr>');
	}
	echo('</table>');
} else {
	echo('<p class="notice">Aucune demande</p>');
}
?>
