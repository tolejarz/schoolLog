<?php
class CalendrierDefaultView extends HtmlView {
	function show($data = array()) {
		$parms = array(
			'lundi' 		=> array(),
			'mardi' 		=> array(),
			'mercredi' 		=> array(),
			'jeudi' 		=> array(),
			'vendredi' 		=> array()
		);
		foreach ($data['jours'] as $row) {
			$parms[$row['jour_libelle']][$row['heure_debut']] = array(
				'id' 				=> $row['id'],
				'jour' 				=> $row['jour'],
				'heure_debut' 		=> $this->FormatDateTimeUsToFr($row['heure_debut']),
				'heure_fin' 		=> $this->FormatDateTimeUsToFr($row['heure_fin']),
				'enseignant' 		=> $row['enseignant'],
				'id_enseignant' 	=> $row['id_enseignant'],
				'classe' 			=> $row['classe'],
				'id_classe' 		=> $row['id_classe'],
				'matiere' 			=> $row['matiere'],
				'id_matiere' 		=> $row['id_matiere'],
				'new' 				=> isset($row['new']) ? $this->FormatDateTimeUsToFr($row['new']) : null,
				'etat'				=> isset($row['etat']) ? $row['etat'] : null,
				'id_operation' 		=> isset($row['id_operation']) ? $row['id_operation'] : null,
				'holidays'			=> isset($row['holidays']) && $row['holidays']
			);
		}
		$parms['_arg'] = $data['_arg'];
		$parms['_week'] = $data['_week'];
		$parms['_displayClasses'] = isset($data['_displayClasses']) && $data['_displayClasses'];
		$parms['_blockDnD'] = isset($data['_blockDnD']) && $data['_blockDnD'];
		$this->_pushTemplate('templates/CalendrierDefault.php', $parms);
	}
}
?>
