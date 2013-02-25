<?php
class MatieresClasseDefaultView extends HtmlView {
	function show($viewparms = array()) {
		$parms = $viewparms;
		$this->_pushTemplate('templates/MatieresClasseDefault.php', $parms);
	}
}
?>
