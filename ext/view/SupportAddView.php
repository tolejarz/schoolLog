<?php
class SupportAddView extends HtmlView {
	function show($viewparms = array()) {
		$parms = $viewparms;
		$this->_pushTemplate('templates/support/add.phtml', $parms);
	}
}
?>
