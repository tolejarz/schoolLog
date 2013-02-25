<?php
echo('
	<div class="selectionBox">
		<form action="index.php?page=supports&amp;action=delete&amp;id=' . $parms["id"] . '" method="post">
			<p class="headerSelectionBox">Etes-vous sûr de vouloir supprimer ce support ?</p>
			<table class="searchContainer">
				<tr><th>Classe :</th><td>' . $parms['classe'] . '</td></tr>
				<tr><th>Matière :</th><td>' . $parms['matiere'] . '</td></tr>
				<tr><th>Titre :</th><td>' . $parms['titre'] . '</td></tr>
				<tr><th>Fichier :</th><td>' . $parms['nom_fichier'] . '</td></tr>
			</table>
			<p class="submitContainer">
				<input name="validation" type="submit" value="Supprimer" />
				<input name="annulation" type="submit" value="Annuler" />
			</p>
		</form>
	</div>');
?>
