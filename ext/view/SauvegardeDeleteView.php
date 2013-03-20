<?php
class SauvegardeDeleteView extends HtmlView {
	function show($viewparms = array()) {
		$parms = array(
			'fichier' 	=> $viewparms['fichier'],
			'date'		=> $this->FormatDateUsToFr($viewparms['date']),
			'heure'		=> $this->FormatTimeUsToFr($viewparms['heure'])
		);
		$this->_pushTemplate('templates/backup/delete.phtml', $parms);
	}
}
?>
