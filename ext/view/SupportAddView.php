<?php
class SupportAddView extends HtmlView {
	function show($viewparms = array()) {
		$parms = $viewparms;
		$this->_pushTemplate('templates/SupportAdd.php', $parms);
	}
}
?>
