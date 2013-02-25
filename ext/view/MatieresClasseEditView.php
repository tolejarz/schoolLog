<?php
class MatieresClasseEditView extends HtmlView {
	function show($viewparms = array()) {
		$parms = $viewparms;
		$this->_pushTemplate('templates/MatieresClasseEdit.php', $parms);
	}
}
?>
