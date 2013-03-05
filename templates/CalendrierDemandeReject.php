<?php
echo('
	<div class="selectionBox">
		<form action="" method="post">
			<p class="headerSelectionBox">Etes-vous sûr de vouloir refuser cette demande ?</p>
			<table class="searchContainer">
				<tr>
					<th>Classe :</th>
					<td>' . $parms['classe'] . '</td>
				</tr>
				<tr>
					<th>Enseignant :</th>
					<td>' . $parms['enseignant'] . '</td>
				</tr>
				<tr>
					<th>Matière :</th>
					<td>' . $parms['matiere'] . '</td>
				</tr>
				<tr>
					<th>Date d\'origine :</th>
					<td>' . $parms['date_origine'] . ' à ' . $parms['heure_origine'] . '</td>
				</tr>
				<tr>
					<th>Date de report :</th>
					<td>' . ($parms['date_report'] != null ? $parms['date_report'] . ' à ' . $parms['heure_report'] : '(non définie)') . '</td>
				</tr>
			</table>
			<p class="submitContainer">
				<input name="validation" type="submit" value="Refuser" />
				<input name="annulation" type="submit" value="Annuler" />
			</p>
		</form>
	</div>');
?>
