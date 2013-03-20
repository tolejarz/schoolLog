<?php
class MatiereDeleteView extends HtmlView {
	function show($viewparms = array()) {
		$parms = $viewparms;
		$this->_pushTemplate('templates/subject/delete.phtml', $parms);
	}
}
?>
