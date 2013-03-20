<?php
$css = array('' => '', 'en attente' => 'pending', 'validée' => 'accepted', 'refusée' => 'refused');
$demands = $parms['demandes'];
echo('
	<h2>Demandes de report</h2>
	<a class="addLink" href="' . Router::build('CalendarRequestAdd') . '" target="_self">' . ($_SESSION['user']['privileges'] == 'superviseur' ? 'Faire une modification' : 'Faire une demande') . '</a>
	<a class="historyLink" href="' . Router::build('CalendarRequestHistory') . '" target="_self">Historique des demandes</a>
	<p class="separator2"></p>');

if (!empty($demands)) {
	echo('
		<table class="listingContainer">
			<tr>' .
				($_SESSION['user']['privileges'] == 'superviseur' ? '<th id="actions">Actions</th>' : '') . '
				<th>Classe</th>' . 
				($_SESSION['user']['privileges'] == 'superviseur' ? '<th>Enseignant</th>' : '') . '
				<th>Matière</th>
				<th id="dateSmall">Date d\'origine</th>
				<th>Date de report</th>
				<th>Etat</th>
			</tr>');
	foreach ($demands as $d) {
		echo('<tr>');
		if ($_SESSION['user']['privileges'] == 'superviseur') {
			if ($d['etat'] == 'en attente') {
				echo('
					<td class="centered">
						<a href="' . Router::build('CalendarRequestAccept', array('request_id' => $d['id'])) . '" target="_self"><img alt="Accepter" src="/img/accept.png" title="Accepter" /></a>
						<a href="' . Router::build('CalendarRequestReject', array('request_id' => $d['id'])) . '" target="_self"><img alt="Refuser" src="/img/refuse.png" title="Refuser" /></a>
					</td>');
			} else {
				echo('<td class="centered">-</td>');
			}
		}
		echo('
				<td class="centered">' . $d['classe'] . '</td>' . 
				($_SESSION['user']['privileges'] == 'superviseur' ? '<td class="centered">' . $d['enseignant'] . '</td>' : '') . '
				<td class="centered">' . $d['matiere'] . '</td>
				<td class="reducedContent">' . $d['date_origine'] . ' à ' . $d['heure_origine'] . '</td>
				<td class="reducedContent">' . ($d['date_report'] != null ? $d['date_report'] . ' à ' . $d['heure_report'] : '(non définie)') . '</td>
				<td class="' . $css[$d['etat']] . '">' . $d['etat'] . '</td>
			</tr>');
	}
	echo('</table>');
} else {
	echo('<p class="notice">Aucune demande</p>');
}
?>
