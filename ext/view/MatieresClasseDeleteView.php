<?php
class MatieresClasseDeleteView extends HtmlView {
	function show($viewparms = array()) {
		$parms = $viewparms;
		$this->_pushTemplate('templates/MatieresClasseDelete.php', $parms);
	}
}
?>
