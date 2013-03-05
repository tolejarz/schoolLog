<?php
echo('
	<div class="selectionBox">
		<form action="" method="post">
			<p class="headerSelectionBox">Veuillez saisir les informations relatives à la matière.</p>
			<table class="searchContainerSmall">
				<tr>
					<th>Nom :</th>
					<td><input name="nom" type="text" value="' . @$_POST['nom'] . '" /></td>
				</tr>
				<tr>
					<th class="top">Classe(s) concernées :</th>
					<td>
						<table class="condensedList">');
				$i = 0;
				foreach ($parms['classes'] as $c) {
					if ($i % 2 == 0) {
							echo('<tr>');
					}
					echo('<td><input name="c_' . $c['id'] . '" type="checkbox" ' . (@$_POST['c_' . $c['id']] ? 'checked=checked' : '') . '/> ' . $c['libelle'] . '</td>');
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
				<input name="validation" type="submit" value="Ajouter" />
				<input name="annulation" type="submit" value="Annuler" />
			</p>
		</form>
	</div>');

?>
