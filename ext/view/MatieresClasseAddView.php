<?php
class MatieresClasseAddView extends HtmlView {
	function show($viewparms = array()) {
		$parms = $viewparms;
		$this->_pushTemplate('templates/MatieresClasseAdd.php', $parms);
	}
}
?>
