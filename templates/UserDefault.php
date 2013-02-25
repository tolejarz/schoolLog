<?php
echo('
	<h2>Liste des enseignants</h2>
	<a class="addLink" href="?page=utilisateurs&amp;action=add" target="_self">Ajouter un enseignant</a>
	<p class="separator3"></p>');
if (empty($parms['enseignants'])) {
	echo('<p class="notice">Aucun enseignant</p>');
} else {
	echo('
		<table class="listingContainerSmall">
			<tr>
				<th id="actions">Actions</th>
				<th>Enseignant(s)</th>
			</tr>');
	foreach ($parms['enseignants'] as $user) {
		echo('
			<tr>
				<td class="centered"><a href="?page=utilisateurs&amp;action=delete&amp;id=' . $user['id'] . '" target="_self"><img alt="Supprimer" src="templates/img/delete.png" title="Supprimer" /> <a href="?page=utilisateurs&amp;action=edit&amp;id=' . $user['id'] . '" target="_self"><img alt="Editer" src="templates/img/edit.png" title="Editer" /></a></td>
				<td>' . $user['civility'] . ' ' . $user['nom'] . '</td>
			</tr>');
	}
	echo('</table>');
}
?>
