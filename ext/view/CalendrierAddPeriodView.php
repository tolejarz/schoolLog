<?php
class CalendrierAddPeriodView extends HtmlView {
	function show($viewparms = array()) {
		$parms = $viewparms;
		$this->_pushTemplate('templates/CalendrierAddPeriod.php', $parms);
	}
}
?>
