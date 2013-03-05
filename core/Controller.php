<?php
abstract class Controller {
	protected $dbo;
	protected $configuration;
	
	function __construct($dbo) {
		$configurator = Configurator::getInstance('config/config.json');
		$this->configuration = $configurator->getConfiguration();
		$this->dbo = $dbo;
	}
	
	protected function _doDefaultAction() {
		
	}
	
	function _getArg($i) {
		return isset($_POST[$i]) ? $_POST[$i] : (isset($_GET[$i]) ? $_GET[$i] : null);
	}
	
	function _pushView($view, $parms = array()) {
		$view->show($parms);
	}
	
	protected function FormatDateTimeFrToUs($i, $includeTime = true) {
		$d = '';
		if (preg_match('#^([0-9]{2})/([0-9]{2})/([0-9]{4})(.*)$#', $i, $f)) {
			$d = sprintf('%04d-%02d-%02d', $f[3], $f[2], $f[1]);
			$t = $f[4];
		} else {
			$t = $i;
		}
		if ($includeTime) {
			if (preg_match('#([0-9]{2}):([0-9]{2})$#', $t, $f)) {
				$d .= sprintf(' %02d:%02d:00', $f[1], $f[2]);
			}
		}
		return trim($d);
	}
	
	protected function FormatTimeFrToUs($i) {
		$d = '';
		if (preg_match('#([0-9]{2}):([0-9]{2})$#', $i, $f)) {
			$d = sprintf(' %02d:%02d:00', $f[1], $f[2]);
		}
		return trim($d);
	}
	
	function perform($a = 'default_action') {
		$a = !empty($_GET['action']) ? $_GET['action'] : $a;
		$parts = explode('_', $a);
		$method = '_do';
		foreach ($parts as $p) {
			$method .= ucfirst($p);
		}
		if (method_exists($this, $method)) {
			$this->$method();
		}
	}
	
	function UnixTimestampFromUsDateTime($i) {
		if (empty($i)) {
			return null;
		}
		$c = explode(' ', $i);
		$cd = explode('-', $c[0]);
		
		if (count($c) == 1) {
			$h = $i = $s = 0;
		} else {
			list($h, $i, $s) = explode(':', $c[1]);
		}
		return mktime(intval($h), intval($i), intval($s), intval($cd[1]), intval($cd[2]), intval($cd[0]));
	}
}
?>
