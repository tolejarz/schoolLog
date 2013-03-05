<?php
echo('
<div class="selectionBox">
	<form method="post" action="">
	<p class="headerSelectionBox">Veuillez saisir les informations relatives au matériel.</p>
		<table class="searchContainer">
			<tr>
				<th>Type de matériel :</th>
				<td><input name="type" type="text" value="' . @$_POST['type'] . '" /></td>
			</tr>
			<tr>
				<th>Modèle :</th>
				<td><input name="modele" type="text" value="' . @$_POST['modele'] . '" /></td>
			</tr>
		</table>
		<p class="submitContainer">
			<input name="validation" type="submit" value="Ajouter" />
			<input name="annulation" type="submit" value="Annuler" />
		</p>
	</form>
</div>');
?>
