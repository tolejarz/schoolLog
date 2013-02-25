<?php
class MaterielDeleteView extends HtmlView {
	function show($viewparms = array()) {
		$parms = $viewparms;
		$this->_pushTemplate('templates/MaterielDelete.php', $parms);
	}
}
?>
