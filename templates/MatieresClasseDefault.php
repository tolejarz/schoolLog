<h2>Liste des matières de la classe <?php echo $parms['classe']; ?></h2>
<a class="addLink" href="<?php echo Router::build('ClassSubjectAdd', array('class_id' => $parms['id_classe'])); ?>" target="_self">Ajouter une matière</a>
<p class="separator3"></p>
<?php
if (empty($parms['subjects'])) {
	echo('<p class="notice">Aucune matière</p>');
} else {
	echo('
		<table class="listingContainer">
			<tr>
				<th id="actions">Actions</th>
				<th>Matière</th>
				<th>Enseignant(s)</th>
			</tr>');
	foreach ($parms['subjects'] as $subject) {
		echo('
			<tr>
				<td class="centered">
					<a href="' . Router::build('ClassSubjectDelete', array('class_id' => $parms['id_classe']), array('id_matiere' => $subject['id'])) . '" target="_self"><img alt="Supprimer" src="/resource/img/delete.png" title="Supprimer" /></a>
					<a href="' . Router::build('ClassSubjectEdit', array('class_id' => $parms['id_classe']), array('id_matiere' => $subject['id'])) . '" target="_self"><img alt="Modifier" src="/resource/img/edit.png" title="Modifier" /></a></td>
				<td>' . $subject['nom'] . '</td>
				<td>');
		if ($subject['enseignants'] == null) {
			echo('aucun');
		} else {
			foreach ($subject['enseignants'] as $e) {
				echo('<p class="thinParagraph">' . $e['civility'] . ' ' . $e['nom'] . '</p>');
			}
		}
		echo('
				</td>
			</tr>');
	}
	echo('</table>');
}
?>
