<?php
if (empty($parms['classes'])) {
	echo('<p class="notice">Aucune classe</p>');
} else {
	foreach ($parms['classes'] as $class) {
		echo('
			<h2>' . $class['libelle'] . '</h2>
			<a class="addLink" href="?page=emploi_du_temps&amp;action=add_period&amp;id_classe=' . $class['id'] . '" target="_self">Ajouter une période</a>
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
						<td class="centered"><a href="?page=emploi_du_temps&amp;action=delete_period&amp;id=' . $period['id'] . '" target="_self"><img alt="Supprimer" src="templates/img/delete.png" title="Supprimer" /> <a href="?page=emploi_du_temps&amp;action=edit_period&amp;id=' . $period['id'] . '" target="_self"><img alt="Editer" src="templates/img/edit.png" title="Editer" /></a></td>
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
