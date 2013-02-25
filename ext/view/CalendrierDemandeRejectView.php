<?php
class CalendrierDemandeRejectView extends HtmlView {
	function show($viewparms = array()) {
		$parms = array(
			'id' 					=> $viewparms['id'],
			'classe' 				=> $viewparms['classe'],
			'enseignant' 			=> $viewparms['enseignant'],
			'matiere' 				=> $viewparms['matiere'],
			'date_origine' 			=> $this->FormatDateTimeUsToFr($viewparms['date_origine'], false),
			'heure_origine' 		=> $this->FormatTimeUsToFr($viewparms['heure_origine']),
			'date_report' 			=> $this->FormatDateTimeUsToFr($viewparms['date_report'], false),
			'heure_report' 			=> $this->FormatTimeUsToFr($viewparms['date_report'])
		);
		$this->_pushTemplate('templates/CalendrierDemandeReject.php', $parms);
	}
}
?>
