<?php
class ClasseDeleteView extends HtmlView {
	function show($viewparms = array()) {
		$parms = $viewparms;
		$this->_pushTemplate('templates/ClasseDelete.php', $parms);
	}
}
?>
