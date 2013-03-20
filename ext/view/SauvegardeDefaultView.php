<?php
class SauvegardeDefaultView extends HtmlView {
	function show($viewparms = array()) {
		$saves = $viewparms['sauvegardes'];
		
		$sauvegardes = array();
		foreach ($saves as $s) {
			$sauvegardes[] = array( 
				'fichier' => $s['fichier'],
				'date' => $this->FormatDateUsToFr($s['date']),
				'heure' => $this->FormatTimeUsToFr($s['heure'])
			);
		}
		$parms = array('sauvegardes' => $sauvegardes);
		$this->_pushTemplate('templates/backup/list.phtml', $parms);
	}
}
?>
