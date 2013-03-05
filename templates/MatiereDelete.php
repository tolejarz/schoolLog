<?php
echo('
	<div class="selectionBox">
		<form action="" method="post">
			<p class="headerSelectionBox">Etes-vous sûr de vouloir supprimer la matière <b>' . $parms['nom'] . '</b> ?</p>
			<p class="submitContainer">
				<input name="validation" type="submit" value="Supprimer" />
				<input name="annulation" type="submit" value="Annuler" />
			</p>
		</form>
	</div>');
?>
