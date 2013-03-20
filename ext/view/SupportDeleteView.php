<?php
class SupportDeleteView extends HtmlView {
	function show($viewparms = array()) {
		$parms = $viewparms;
		$this->_pushTemplate('templates/support/delete.phtml', $parms);
	}
}
?>
