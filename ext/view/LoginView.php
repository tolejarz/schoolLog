<?php
class LoginView extends HtmlView {
	function show($viewparms = array()) {
		$parms = $viewparms;
		$this->_pushTemplate('templates/login.php', $parms);
	}
}
?>
