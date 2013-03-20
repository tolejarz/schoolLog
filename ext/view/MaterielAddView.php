<?php
class MaterielAddView extends HtmlView {
	function show($viewparms = array()) {
		$parms = $viewparms;
		$this->_pushTemplate('templates/equipment/add.phtml', $parms);
	}
}
?>
