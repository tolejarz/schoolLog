<?php
class SauvegardeAddView extends HtmlView {
	function show($viewparms = array()) {
		$parms = $viewparms;
		$this->_pushTemplate('templates/SauvegardeAdd.php', $parms);
	}
}
?>
