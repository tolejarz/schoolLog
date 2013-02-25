<?php
echo('
	<div class="selectionBox">
		<form action="index.php?page=sauvegardes&amp;action=delete&amp;fichier=' . $parms['fichier'] . '" method="post">
			<p class="headerSelectionBox">Etes-vous sûr de vouloir supprimer la sauvegarde du <b>' . $parms['date'] . ' à ' . $parms['heure'] . '</b> ?</p>
			<p class="submitContainer">
				<input name="validation" type="submit" value="Supprimer" />
				<input name="annulation" type="submit" value="Annuler" />
			</p>
		</form>
	</div>');
?>
