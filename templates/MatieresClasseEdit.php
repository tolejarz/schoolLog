<?php
echo('
	<div class="selectionBox">
		<form action="index.php?page=matieresClasse&amp;action=edit&id_classe=' . $parms['id_classe'] . '&id_matiere=' . $parms['id_matiere'] . '" method="post">
			<p class="headerSelectionBox">Veuillez choisir les enseignants affectés à la matière <b>' . $parms['matiere'] . '</b> de la classe <b>' . $parms['classe'] . '</b>.</p>
			<table class="searchContainer">
				<tr>
					<td>
						<table class="condensedList">');
					$i = 0;
					foreach ($parms['enseignants'] as $e) {
						if ($i % 2 == 0) {
							echo('<tr>');
						}
						echo('<td><input ' . (in_array($e['id'], $parms['enseignants_matiere']) ? 'checked="checked" ' : '') . 'name="e_' . $e['id'] . '" type="checkbox" />' . $e['civility'] . ' ' . $e['nom'] . '</td>');
						$i++;
						if ($i % 2 == 0) {
							echo('</tr>');
						}
					}
					if ($i % 2 == 1) {
						echo('</tr>');
					}
					echo('
						</table>
					</td>
				</tr>
			</table>
			<p class="submitContainer">
				<input name="validation" type="submit" value="Valider" />
				<input name="annulation" type="submit" value="Annuler" />
			</p>
		</form>
	</div>');
?>
