<?php
echo('
<div class="selectionBox">
	<form method="post" action="index.php?page=sauvegardes&amp;action=add">
	<p class="headerSelectionBox">Etes-vous sur de vouloir sauvegarder la base de données ?</p>
		<p class="submitContainer">
			<input name="validation" type="submit" value="Sauvegarder" />
			<input name="annulation" type="submit" value="Annuler" />
		</p>
	</form>
</div>');
?>
