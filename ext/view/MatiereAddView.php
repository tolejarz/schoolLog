<?php
class MatiereAddView extends HtmlView {
	function show($viewparms = array()) {
		$parms = $viewparms;
		$this->_pushTemplate('templates/MatiereAdd.php', $parms);
	}
}
?>
