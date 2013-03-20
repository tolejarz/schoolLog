<?php
class UserDefaultView extends HtmlView {
	function show($viewparms = array()) {
		$parms = $viewparms;
		$this->_pushTemplate('templates/user/list.phtml', $parms);
	}
}
?>
