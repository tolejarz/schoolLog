<?php
class MatiereEditView extends HtmlView {
	function show($viewparms = array()) {
		$parms = $viewparms;
		$this->_pushTemplate('templates/subject/edit.phtml', $parms);
	}
}
?>
