<?php
echo('
	<div class="selectionBox">
		<form action="index.php?page=classes&amp;action=delete&amp;id=' . $parms['id'] . '" method="post">
			<p class="headerSelectionBox">Etes-vous sûr de vouloir supprimer la classe <b>' . $parms['libelle'] . '</b> ?</p>
			<p class="submitContainer">
				<input name="validation" type="submit" value="Supprimer" />
				<input name="annulation" type="submit" value="Annuler" />
			</p>
		</form>
	</div>');
?>