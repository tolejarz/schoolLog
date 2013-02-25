<?php
echo('
	<div class="selectionBox">
		<form action="index.php?page=matieres&amp;action=edit&amp;id=' . $parms['id'] . '" method="post">
			<p class="headerSelectionBox">Veuillez modifier les informations relatives à la matière.</p>
			<table class="searchContainerSmall">
				<tr>
					<th>Nom :</th>
					<td><input name="nom" type="text" value="' . (isset($_POST['nom'] ) ? $_POST['nom'] : $parms['nom']) . '" /></td>
				</tr>
			</table>
			<p class="submitContainer">
				<input name="validation" type="submit" value="Valider" />
				<input name="annulation" type="submit" value="Annuler" />
			</p>
		</form>
	</div>');
?>
