<?php
class MatiereDefaultView extends HtmlView {
	function show($viewparms = array()) {
		$parms = $viewparms;
		$this->_pushTemplate('templates/MatiereDefault.php', $parms);
	}
}
?>
