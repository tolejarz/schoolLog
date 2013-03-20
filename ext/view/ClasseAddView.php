<?php
class ClasseAddView extends HtmlView {
	function show($viewparms = array()) {
		$parms = $viewparms;
		$this->_pushTemplate('templates/class/add.phtml', $parms);
	}
}
?>
