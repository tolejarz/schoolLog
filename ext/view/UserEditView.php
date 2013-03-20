<?php
class UserEditView extends HtmlView {
	function show($viewparms = array()) {
		$parms = $viewparms;
		$this->_pushTemplate('templates/user/edit.phtml', $parms);
	}
}
?>
