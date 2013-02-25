<?php
class CalendrierDeletePeriodView extends HtmlView {
	function show($viewparms = array()) {
		$parms = $viewparms;
		$parms['date_debut'] = $this->FormatDateTimeUsToFr($parms['date_debut'], false);
		$parms['date_fin'] = $this->FormatDateTimeUsToFr($parms['date_fin'], false);
		$this->_pushTemplate('templates/CalendrierDeletePeriod.php', $parms);
	}
}
?>
