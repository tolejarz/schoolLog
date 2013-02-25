<?php
class CalendrierShowPeriodesView extends HtmlView {
	function show($viewparms = array()) {
		$parms = $viewparms;
		foreach ($parms['classes'] as &$class) {
			foreach ($class['periods'] as &$period) {
				$period['date_debut'] = $this->FormatDateTimeUsToFr($period['date_debut'], false);
				$period['date_fin'] = $this->FormatDateTimeUsToFr($period['date_fin'], false);
			}
		}
		$this->_pushTemplate('templates/CalendrierShowPeriodes.php', $parms);
	}
}
?>
