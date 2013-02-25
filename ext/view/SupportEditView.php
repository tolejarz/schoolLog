<?php
class SupportEditView extends HtmlView {
	function show($viewparms = array()) {
		$parms = $viewparms;
		$this->_pushTemplate('templates/SupportEdit.php', $parms);
	}
}
?>
