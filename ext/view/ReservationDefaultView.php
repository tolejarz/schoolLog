<?php
class ReservationDefaultView extends HtmlView {
	function show($data = array(), $materiels = array()) {
		$parms = array(
			'lundi' => array(),
			'mardi' => array(),
			'mercredi' => array(),
			'jeudi' => array(),
			'vendredi' => array()
		);
		foreach ($data as $row) {
			$parms[$row['jour_libelle']][$row['heure_debut']] = array(
				'id'				=> $row['id'],
				'date_heure_debut'	=> $row['date_heure_debut'],
				'heure_debut' 		=> $this->FormatDateTimeUsToFr($row['heure_debut']),
				'heure_fin' 		=> $this->FormatDateTimeUsToFr($row['heure_fin']),
				'etat' 				=> $row['etat_reservation'],
				'enseignant' 		=> $row['enseignant'],
				'id_enseignant' 	=> $row['id_enseignant'],
				'etat_materiel' 	=> $row['etat_materiel']
			);
		}
		$parms['id_materiel'] = $data['id_materiel'];
		$parms['_arg'] = $data['_arg'];
		$parms['_week'] = $data['_week'];
		$parms['materiels'] = $materiels;
		$this->_pushTemplate('templates/booking/list.phtml', $parms);
	}
}
?>
