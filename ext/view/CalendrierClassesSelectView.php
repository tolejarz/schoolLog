<?php
class CalendrierClassesSelectView extends HtmlView {
	function show($viewparms = array()) {
		$parms = $viewparms;
		$this->_pushTemplate('templates/CalendrierClassesSelect.php', $parms);
	}
}
?>
