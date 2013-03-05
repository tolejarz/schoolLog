<?php
echo('
	<div class="selectionBox">
		<form method="post" action="">
			<p class="headerSelectionBox">Veuillez saisir les informations relatives à la classe.</p>
			<table class="searchContainer">
				<tr>
					<th>Libellé :</th>
					<td><input name="libelle" type="text" value="' . @$_POST['libelle'] . '" /></td>
				</tr>
				<tr>
					<th>Email :</th>
					<td><input name="email" type="text" value="' . @$_POST['email'] . '"/></td>
				</tr>
			</table>
			<p class="submitContainer">
				<input name="validation" type="submit" value="Ajouter" />
				<input name="annulation" type="submit" value="Annuler" />
			</p>
		</form>
	</div>');
?>
