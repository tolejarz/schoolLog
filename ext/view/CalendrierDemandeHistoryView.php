<?php
class CalendrierDemandehistoryView extends HtmlView {
	function show($viewparms = array()) {
		$parms = $viewparms;
		foreach ($parms['demandes'] as $demande => &$values) {
			$values = array(
				'classe'		=> $values['classe'],
				'matiere'		=> $values['matiere'],
				'etat'			=> $values['etat'],
				'enseignant'	=> $values['enseignant'],
				'id'			=> $values['id'],
				'date_origine'	=> $this->FormatDateUsToFr($values['date_origine']),
				'heure_origine'	=> $this->FormatTimeUsToFr($values['heure_debut']),
				'date_report'	=> $this->FormatDateTimeUsToFr($values['date_report'], false),
				'heure_report'	=> $this->FormatTimeUsToFr($values['date_report'])
			);
		}
		$this->_pushTemplate('templates/CalendrierDemandeHistory.php', $parms);
	}
}
?>
