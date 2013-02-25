<?php
class UserDeleteView extends HtmlView {
	function show($viewparms = array()) {
		$parms = $viewparms;
		$this->_pushTemplate('templates/UserDelete.php', $parms);
	}
}
?>
