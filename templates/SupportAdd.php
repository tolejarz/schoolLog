<?php
echo('
<div class="selectionBox">
	<form id="form_upload" action="" enctype="multipart/form-data" method="post">
		<p class="headerSelectionBox">Veuillez saisir les informations relatives au support.</p>
		<table class="searchContainer">');
		if ($_SESSION['user_privileges'] == 'superviseur') {
			echo('
			<tr>
				<th>Matière/Enseignant :</th>
				<td>
					<select name="mat_prof">');
					foreach ($parms['matieres'] as $m) {
						foreach ($m['profs'] as $p) {
							$cat = $m['id'] . ';' . $p['id'];
							echo('<option value="' . $cat . '" ' . (($cat == $parms['mat_prof']) ? 'selected' : '') . '>' . $m['nom'] . ' (' . $p['nom'] . ')</option>');
						}
					}
					echo('				
					</select>
				</td>
			</tr>');
		}
		echo('
			<tr>
				<th>Titre du support :</th>
				<td><input name="nom_support" type="text" value="' . @$parms['nom_support'] . '" /></td>
			</tr>
			<tr>
				<th class="top">Mots-clés :</th>
				<td><input type="text" name="tags" value="' . @$parms['tags'] . '" /><p class="indication">(séparés par des ";")</p></td>
			</tr>
			<tr>
				<th class="top">Fichier :</th>
				<td>
					<input name="MAX_FILE_SIZE" type="hidden" value="26214400" />
					<input type="hidden" name="APC_UPLOAD_PROGRESS" id="progress_key" value="12345"/>
					<input name="nom_du_fichier" type="file" />
					<p class="indication">(pas d\'exe, taille max. : 25Mo)</p>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="text-align:center;"><div id="progressbar"/></td>
			</tr>
		</table>
		<p class="submitContainer">
			<input name="validation" type="submit" value="Ajouter" />
			<input name="annulation" type="submit" value="Annuler" />
		</p>						
	</form>
</div>'); 
?>
