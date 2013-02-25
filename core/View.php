<?php
abstract class View {
	protected function FormatDateTimeUsToFr($i, $includeTime = true) {
		$d = '';
		if (preg_match('#^([0-9]{4})-([0-9]{2})-([0-9]{2})(.*)$#', $i, $f)) {
			$d = sprintf('%02d/%02d/%04d', $f[3], $f[2], $f[1]);
			$t = $f[4];
		} else {
			$t = $i;
		}
		if ($includeTime) {
			if (preg_match('#([0-9]{2}):([0-9]{2}):([0-9]{2})$#', $t, $f)) {
				$d .= sprintf(' %02d:%02d', $f[1], $f[2]);
			}
		}
		return trim($d);
	}
	
	protected function FormatDateUsToFr($i) {
		if (preg_match('#^([0-9]{4})-([0-9]{2})-([0-9]{2})$#', $i, $f)) {
			return sprintf('%02d/%02d/%04d', $f[3], $f[2], $f[1]);
		}
		return null;
	}
	
	protected function FormatTimeUsToFr($i) {
		if (preg_match('#([0-9]{2}):([0-9]{2}):([0-9]{2})$#', $i, $f)) {
			return sprintf('%02d:%02d', $f[1], $f[2]);
		}
		return null;
	}
	
	abstract function show($parms = array());
}
?>
