<h2>Liste des classes</h2>
<a class="addLink" href="<?php echo Router::build('ClassAdd'); ?>" target="_self">Ajouter une classe</a>
<p class="separator3"></p>
<?php
if (empty($parms['classes'])) {
	echo('<p class="notice">Aucune classe</p>');
} else {
	echo('
		<table class="listingContainer">
			<tr>
				<th id="actions">Actions</th>
				<th>Libellé</th>
				<th>E-mail</th>
				<th id="actions">Matières</th>
			</tr>');
	foreach ($parms['classes'] as $class) {
		echo('
			<tr>
				<td class="centered"><a href="' . Router::build('ClassDelete', array('class_id' => $class['id'])) . '" target="_self"><img alt="Supprimer" src="templates/img/delete.png" title="Supprimer" /></a> <a href="' . Router::build('ClassEdit', array('class_id' => $class['id'])) . '" target="_self"><img alt="Modifier" src="templates/img/edit.png" title="Modifier" /></a></td>
				<td class="centered">' . $class['libelle'] . '</td>
				<td class="centered">' . $class['email'] . '</td>
				<td class="centered"><a href="' . Router::build('ClassSubjectList', array('class_id' => $class['id'])) . '" target="_self"><img alt="Visualiser" src="templates/img/info.png" title="Visualiser" /></a></td>
			</tr>');
	}
	echo('</table>');
}
?>
