<?php
class SupportSearchView extends HtmlView {
	function show($viewparms = array()) {
		$parms = $viewparms;
		$this->_pushTemplate('templates/SupportSearch.php', $parms);
	}
}
?>
