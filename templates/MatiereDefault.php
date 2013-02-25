<?php
echo('
	<h2>Liste des matières</h2>
	<a class="addLink" href="?page=matieres&amp;action=add" target="_self">Ajouter une matière</a>
	<p class="separator3"></p>');
if (empty($parms['subjects'])) {
	echo('<p class="notice">Aucune matière</p>');
} else {
	echo('
		<table class="listingContainerSmall">
			<tr>
				<th id="actions">Actions</th>
				<th>Nom</th>
			</tr>');
	foreach ($parms['subjects'] as $subject) {
		echo('
			<tr>
				<td class="centered"><a href="?page=matieres&amp;action=delete&amp;id=' . $subject['id'] . '" target="_self"><img alt="Supprimer" src="templates/img/delete.png" title="Supprimer" /></a> <a href="?page=matieres&amp;action=edit&amp;id=' . $subject['id'] . '" target="_self"><img alt="Modifier" src="templates/img/edit.png" title="Modifier" /></a></td>
				<td>' . $subject['nom'] . '</td>
			</tr>');
	}
	echo('</table>');
}
?>