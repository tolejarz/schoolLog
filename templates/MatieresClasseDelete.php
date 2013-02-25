<?php
echo('
	<div class="selectionBox">
			<form action="index.php?page=matieresClasse&amp;action=delete&amp;id_classe=' . $parms['id_classe'] . '&amp;id_matiere=' . $parms['id_matiere'] . '" method="post">
			<p class="headerSelectionBox">Etes-vous sûr de vouloir supprimer la matière <b>' . $parms['matiere'] . '</b> de la classe <b>' . $parms['classe'] . '</b> ?</p>
			<p class="submitContainer">
				<input name="validation" type="submit" value="Supprimer" />
				<input name="annulation" type="submit" value="Annuler" />
			</p>
		</form>
	</div>');
?>
