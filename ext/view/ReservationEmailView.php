<?php
class ReservationEmailView extends View {
	function show($viewparms = array()) {
		$parms = array(
			'enseignant'		=> $viewparms['enseignant'],
			'type' 				=> $viewparms['type'],
			'modele' 			=> $viewparms['modele'],
			'date' 				=> $this->FormatDateTimeUsToFr($viewparms['date_heure_debut'], false), 
			'heure_debut' 		=> $this->FormatTimeUsToFr($viewparms['date_heure_debut']),
			'heure_fin' 		=> $this->FormatTimeUsToFr($viewparms['date_heure_fin'])
		);
		return $parms;
	}
}
?>
