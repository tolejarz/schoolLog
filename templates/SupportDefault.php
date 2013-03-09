<?php
if (!empty($parms['classes'])) {
	foreach ($parms['classes'] as $class) {
		echo($_SESSION['user_privileges'] != 'eleve' ? '<h2>' . $class['libelle'] . '</h2>' : '');
		if (!empty($class['subjects'])) {
			foreach ($class['subjects'] as $subject) {
				echo(
					(in_array($_SESSION['user_privileges'], array('enseignant', 'superviseur')) ? '<a class="addLink" href="' . Router::build('SupportAdd', NULL, array('class' => $class['id'], 'subject_id' => $subject['id'])) . '" target="_self">Ajouter un support</a>' : '') . '
					<h3' . ($_SESSION['user_privileges'] == 'enseignant' ? ' id="' . $class['id'] . '-' . $subject['id'] . '"' : '') . '>' . $subject['nom'] . '</h3>
					<p class="separator4"></p>');
				if (!empty($subject['supports'])) {
					echo('
						<table class="listingContainer">
							<tr>
								' . (in_array($_SESSION['user_privileges'], array('enseignant', 'superviseur')) ? '<th id="actions">Actions</th>' : '') . '
								<th>Date d\'envoi</th>
								' . (in_array($_SESSION['user_privileges'], array('eleve', 'superviseur')) ? '<th>Enseignant</th>' : '') . '
								<th>Titre du support</th>
								<th>Fichier</th>
							</tr>');
					foreach ($subject['supports'] as $support) {
						echo('
							<tr>' .
								(in_array($_SESSION['user_privileges'], array('enseignant', 'superviseur')) ? '<td class="centered"><a href="' . Router::build('SupportDelete', array('support_id' => $support['id'])) . '" target="_self"><img alt="Supprimer" src="/resource/img/delete.png" title="Supprimer" /></a>' : '') .
								($_SESSION['user_privileges'] == 'enseignant' ? ' <a href="' . Router::build('SupportEdit', array('support_id' => $support['id'])) . '" target="_self"><img alt="Modifier" src="/resource/img/edit.png" title="Modifier" /></a>' : '') .
								(in_array($_SESSION['user_privileges'], array('enseignant', 'superviseur')) ? '</td>' : '') . '
								<td class="centeredThin">' . $support['date'] . '</td>' .
								(in_array($_SESSION['user_privileges'], array('eleve', 'superviseur')) ? '<td class="centeredThin">' . $support['enseignant'] . '</td>' : '') . '
								<td>' . $support['titre'] . '</td>
								<td class="centeredThin"><a class="downloadLink" href="' . UPLOAD_PATH . $support['nom_fichier'] . '" target="_blank">Télécharger</a></td>
							</tr>');
					}
					echo('</table>');
				} else {
					echo('<p class="notice">Aucun support</p>');
				}
				echo('<p class="separator2"></p>');
			}
		} else {
			echo('<p class="notice">Aucun support</p>');
		}
		echo('<p class="separator1"></p>');
	}
} else {
	echo ($_SESSION['user_privileges'] == 'enseignant' ? '<p class="notice">Vous n\'enseignez dans aucune classe</p>' : '<p class="notice">Aucun support trouvé</p>');
}
?>
