<?php
class ClasseEditView extends HtmlView {
	function show($viewparms = array()) {
		$parms = $viewparms;
		$this->_pushTemplate('templates/class/edit.phtml', $parms);
	}
}
?>
