<?php
class ClasseDefaultView extends HtmlView {
	function show($viewparms = array()) {
		$parms = $viewparms;
		$this->_pushTemplate('templates/ClasseDefault.php', $parms);
	}
}
?>
