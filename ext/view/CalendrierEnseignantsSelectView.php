<?php
class CalendrierEnseignantsSelectView extends HtmlView {
	function show($viewparms = array()) {
		$parms = $viewparms;
		$this->_pushTemplate('templates/CalendrierEnseignantsSelect.php', $parms);
	}
}
?>
