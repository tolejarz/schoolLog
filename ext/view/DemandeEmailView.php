<?php
class DemandeEmailView extends View {
	function show($viewparms = array()) {
		$parms = array(
			'enseignant' 		=> $viewparms['enseignant'],
			'matiere' 			=> $viewparms['matiere'],
			'date_origine' 		=> $this->FormatDateTimeUsToFr($viewparms['date_origine'], false), 
			'heure_origine' 	=> $this->FormatTimeUsToFr($viewparms['heure_origine']),
			'date_report' 		=> $this->FormatDateTimeUsToFr($viewparms['date_report'], false),
			'heure_report' 		=> $this->FormatTimeUsToFr($viewparms['date_report'])
		);
		return $parms;
	}
}
?>
