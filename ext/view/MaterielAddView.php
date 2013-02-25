<?php
class MaterielAddView extends HtmlView {
	function show($viewparms = array()) {
		$parms = $viewparms;
		$this->_pushTemplate('templates/MaterielAdd.php', $parms);
	}
}
?>
