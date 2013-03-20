<?php
class MaterielDeleteView extends HtmlView {
	function show($viewparms = array()) {
		$parms = $viewparms;
		$this->_pushTemplate('templates/equipment/delete.phtml', $parms);
	}
}
?>
