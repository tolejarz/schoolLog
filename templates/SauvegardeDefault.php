<?php
$sauvegardes = $parms['sauvegardes'];
echo('
	<h2>Liste des sauvegardes</h2>
	<a class="addLink" href="?page=sauvegardes&amp;action=add" target="_self">Sauvegarder la base</a>
	<p class="separator2"></p>');
if (!empty($sauvegardes)) {
	echo('
		<table class="listingContainerSmall">
			<tr>
				<th id="actions">Action</th>
				<th>Date</th>
				<th>Heure</th>
				<th>Restaurer</th>
			</tr>');
	foreach ($sauvegardes as $sauvegarde) {
		echo('
			<tr><td class="centered"><a href="?page=sauvegardes&amp;action=delete&fichier=' . $sauvegarde['fichier'] . '" target="_self"><img alt="Supprimer" src="templates/img/delete.png" title="Supprimer" /></a></td>
				<td class="centered">' . $sauvegarde['date'] . '</td><td class="centered">' . $sauvegarde['heure'] . '</td>
				<td class="centered"><a href="?page=sauvegardes&amp;action=restore&fichier=' . $sauvegarde['fichier'] . '" target="_self"><img alt="Restaurer" src="templates/img/dbrestore.png" title="Restaurer" /></a></td>
			</tr>');
	}
	echo('</table>');
} else {
	echo('<p class="notice">Aucune sauvegarde</p>');
}
?>
