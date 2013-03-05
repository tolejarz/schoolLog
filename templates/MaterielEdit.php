<?php
$etat = isset($_POST['etat']) ? $_POST['etat'] : $parms['etat'];
echo('
	<div class="selectionBox">
		<form action="" method="post">
			<p class="headerSelectionBox">Veuillez modifier les informations relatives au matériel.</p>
			<table class="searchContainer">
				<tr>
					<th>Type de matériel :</th>
					<td><input name="type" type="text" value="' . (isset($_POST['type'] ) ? $_POST['type'] : $parms['type']) . '" /></td>
				</tr>
				<tr>
					<th>Modèle :</th>
					<td><input name="modele" type="text" value="' . (isset($_POST['modele'] ) ? $_POST['modele'] : $parms['modele']) . '" /></td>
				</tr>
				<tr>
					<th>Etat :</th>
					<td>
						<select name="etat">
							<option ' . ($etat == "fonctionnel" ? 'selected="selected" ' : '') . ' value="fonctionnel">Fonctionnel</option>
							<option ' . ($etat == "en maintenance" ? 'selected="selected" ' : '') . ' value="en maintenance">En maintenance</option>
						</select>
					</td>
				</tr>
			</table>
			<p class="submitContainer">
				<input name="validation" type="submit" value="Valider" />
				<input name="annulation" type="submit" value="Annuler" />
			</p>
		</form>
	</div>');
?>
