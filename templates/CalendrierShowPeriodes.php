<?php
if (empty($parms['classes'])) {
	echo('<p class="notice">Aucune classe</p>');
} else {
	foreach ($parms['classes'] as $class) {
		echo('
			<h2>' . $class['libelle'] . '</h2>
			<a class="addLink" href="' . Router::build('CalendarPeriodAdd', NULL, array('class_id' => $class['id'])) . '" target="_self">Ajouter une période</a>
			<p class="separator3"></p>');
		if (empty($class['periods'])) {
			echo('<p class="notice">Aucune période</p>');
		} else {
			echo('
				<table class="listingContainerSmall">
					<tr>
						<th class="actions">Actions</th>
						<th>Type</th>
						<th>Date de début</th>
						<th>Date de fin</th>
					</tr>');
			foreach ($class['periods'] as $period) {
				echo('
					<tr>
						<td class="centered">
							<a href="' . Router::build('CalendarPeriodDelete', array('period_id' => $period['id'])) . '" target="_self"><img alt="Supprimer" src="/resource/delete.png" title="Supprimer" />
							<a href="' . Router::build('CalendarPeriodEdit', array('period_id' => $period['id'])) . '" target="_self"><img alt="Editer" src="/resource/edit.png" title="Editer" /></a>
						</td>
						<td class="centered">' . $period['type'] . '</td>
						<td class="centered">' . $period['date_debut'] . '</td>
						<td class="centered">' . $period['date_fin'] . '</td>
					</tr>');
			}
			echo('</table></p>');
		}
		echo('<p class="separator2">');
	}
}
?>
