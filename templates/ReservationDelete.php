<?php
echo('
	<div class="selectionBox">
		<form action="index.php?page=reservations&amp;action=delete&amp;id=' . $parms['id'] . '&amp;id_materiel=' . $parms['id_materiel'] . '" method="post">
			<p class="headerSelectionBox">Etes-vous sûr de vouloir supprimer cette réservation ?</p>
			<table class="searchContainer">
				<tr>
					<th>Date :</th>
					<td>' . $parms['date_reservation'] . '</td>
				</tr>
				<tr>
					<th>Heure de début :</th>
					<td>' . $parms['heure_debut'] . '</td>
				</tr>
				<tr>
					<th>Heure de fin :</th>
					<td>' . $parms['heure_fin'] . '</td>
				</tr>');
if ($_SESSION['user_privileges'] == 'superviseur') {
	echo('
				<tr>
					<th>Enseignant :</th>
					<td>' . $parms['enseignant'] . '</td>
				</tr>');
}
echo('
				<tr>
					<th>Matériel :</th>
					<td>' . $parms['materiel'] . '</td>
				</tr>
			</table>
			<p class="submitContainer">
				<input name="validation" type="submit" value="Supprimer" />
				<input name="annulation" type="submit" value="Annuler" />
			</p>
		</form>
	</div>');
?>
