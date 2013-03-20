<?php
class MaterielEditView extends HtmlView {
	function show($viewparms = array()) {
		$parms = $viewparms;
		$this->_pushTemplate('templates/equipment/edit.phtml', $parms);
	}
}
?>
