<?php
class MaterielDefaultView extends HtmlView {
	function show($viewparms = array()) {
		$parms = $viewparms;
		$this->_pushTemplate('templates/MaterielDefault.php', $parms);
	}
}
?>
