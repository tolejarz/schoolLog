<?php
echo('<h2>Liste du matériel</h2>');
if ($_SESSION['user_privileges'] == 'superviseur') {
	echo('
		<a class="addLink" href="' . Router::build('EquipmentAdd') . '" target="_self">Ajouter un matériel</a>
		<p class="separator3"></p>');
}
if (empty($parms['equipments'])) {
	echo('<p class="notice">Aucun matériel</p>');
} else {
	echo('
		<table class="listingContainer">
			<tr>
				' . ($_SESSION['user_privileges'] == 'superviseur' ? '<th id="actions">Actions</th>' : '') . '
				<th>Type</th>
				<th>Modèle</th>
				' . ($_SESSION['user_privileges'] == 'enseignant' ? '<th>Réserver</th>' : '') . '
				' . ($_SESSION['user_privileges'] == 'superviseur' ? '<th>Etat</th>' : '') . '
			</tr>');
	foreach ($parms['equipments'] as $equipment) {
		echo('
			<tr>
				' . ($_SESSION['user_privileges'] == 'superviseur' ? '<td class="centered"><a href="' . Router::build('EquipmentDelete', array('equipment_id' => $equipment['id'])) . '" target="_self"><img alt="Supprimer" src="templates/img/delete.png" title="Supprimer" /></a> <a href="' . Router::build('EquipmentEdit', array('equipment_id' => $equipment['id'])) . '" target="_self"><img alt="Modifier" src="templates/img/edit.png" title="Modifier" /></a></td>' : '') . '
				<td>' . $equipment['type'] . '</td>
				<td>' . $equipment['modele'] . '</td>
				' . ($_SESSION['user_privileges'] == 'enseignant' ? ($equipment['etat'] == 'fonctionnel' ? '<td class="centered"><a href="?page=reservations&amp;action=add&amp;id_materiel=' . $equipment['id'] . '" target="_self"><img alt="Réserver" src="templates/img/reserve.png" title="Réserver" /></a></td>' : '<td class="refused"><b>' . $equipment['etat'] . '</b></td>') : '') . '
				' . ($_SESSION['user_privileges'] == 'superviseur' ? ($equipment['etat'] == 'fonctionnel' ? '<td class="accepted">' : '<td class="refused">') . '<b>' . $equipment['etat'] . '</b></td>' : '') . '
			</tr>');
	}
	echo('</table>');
}
?>
