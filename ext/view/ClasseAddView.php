<?php
class ClasseAddView extends HtmlView {
	function show($viewparms = array()) {
		$parms = $viewparms;
		$this->_pushTemplate('templates/ClasseAdd.php', $parms);
	}
}
?>
