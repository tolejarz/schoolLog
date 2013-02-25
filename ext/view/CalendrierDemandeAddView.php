<?php
class CalendrierDemandeAddView extends HtmlView {
	function show($viewparms = array()) {
		$parms = $viewparms;
		$this->_pushTemplate('templates/CalendrierDemandeAdd.php', $parms);
	}
}
?>
