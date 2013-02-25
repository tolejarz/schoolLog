<?php
class MatieresClasseSelectView extends HtmlView {
	function show($viewparms = array()) {
		$parms = $viewparms;
		$this->_pushTemplate('templates/MatieresClasseSelect.php', $parms);
	}
}
?>
