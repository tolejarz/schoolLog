<?php
echo('
	<div class="selectionBox">
		<form action="index.php?page=sauvegardes&amp;action=restore&amp;fichier=' . $parms['fichier'] . '" method="post">
			<p class="headerSelectionBox">Etes-vous sûr de vouloir restaurer la sauvegarde du <b>' . $parms['date'] . ' à ' . $parms['heure'] . '</b> ?</p>
			<table class="searchContainer">
			<p class="submitContainer">
				<input name="validation" type="submit" value="Valider" />
				<input name="annulation" type="submit" value="Annuler" />
			</p>
		</form>
	</div>');
?>
