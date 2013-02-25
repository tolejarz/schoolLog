<?php
class ReservationAddView extends HtmlView {
	function show($viewparms = array()) {
		$parms = $viewparms;
		$this->_pushTemplate('templates/ReservationAdd.php', $parms);
	}
}
?>
