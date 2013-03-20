<div class="selectionBox">
	<form action="" method="post">
		<p class="headerSelectionBox">Etes-vous sûr de vouloir supprimer cette période ?</p>
			<table class="searchContainer">
				<tr><th>Type :</th><td><?php echo($parms['type']); ?></td></tr>
				<tr><th>Date de début :</th><td><?php echo($parms['date_debut']); ?></td></tr>
				<tr><th>Date de fin :</th><td><?php echo($parms['date_fin']); ?></td></tr>
				<tr><th>Classe :</th><td><?php echo($parms['nom_classe']); ?></td></tr>
			</table>
			<p class="submitContainer">
				<input name="validation" type="submit" value="Supprimer" />
				<input name="annulation" type="submit" value="Annuler" />
			</p>
	</form>
</div>
