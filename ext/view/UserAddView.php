<?php
class UserAddView extends HtmlView {
	function show($viewparms = array()) {
		$parms = $viewparms;
		$this->_pushTemplate('templates/UserAdd.php', $parms);
	}
}
?>
