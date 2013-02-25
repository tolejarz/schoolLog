<?php
echo('
	<div class="selectionBox">
		<form action="index.php?page=supports&amp;action=edit&amp;id=' . $parms["id"] . '" method="post">
			<input name="nom_fichier" type="hidden" value="' . $parms['nom_fichier'] . '" />
			<p class="headerSelectionBox">Veuillez modifier les informations relatives au support.</p>
			<table class="searchContainer">
				<tr>
					<th>Matière/Classe :</th>
					<td>
						<select name="id_classe_id_matiere">');
foreach ($parms["classes"] as $c) {
	echo('<optgroup label="' . $c['libelle'] . '">');
	foreach ($c["subjects"] as $s) {
		echo ('<option ' . ($c['libelle'] == $parms['classe'] && $s['nom'] == $parms['matiere'] ? 'selected="selected" ' : '') . 'value="' . $c['id'] . ';' . $s['id'] . '">' . $s['nom'] . '</option>');
	}
	echo('</optgroup>');
}
echo('
					</td>
				</tr>
				<tr>
					<th>Titre :</th>
					<td><input name="titre" type="text" value="' . $parms['titre'] . '" /></td>
				</tr>
				<tr>
					<th>Mots-clés :</th>
					<td><input name="tags" type="text" value="' . $parms['tags'] . '" /> (séparés par des ";")</td>
				</tr>
			</table>
			<p class="submitContainer">
				<input name="validation" type="submit" value="Valider" />
				<input name="annulation" type="submit" value="Annuler" />
			</p>
		</form>
	</div>');
?>
