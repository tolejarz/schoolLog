<?php
class SupportDefaultView extends HtmlView {
	function show($viewparms = array()) {
		$parms = $viewparms;
		$this->_pushTemplate('templates/support/list.phtml', $parms);
	}
}
?>
