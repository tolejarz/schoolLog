<?php
class SupportDeleteView extends HtmlView {
	function show($viewparms = array()) {
		$parms = $viewparms;
		$this->_pushTemplate('templates/SupportDelete.php', $parms);
	}
}
?>
