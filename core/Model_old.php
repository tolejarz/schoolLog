<?php
abstract class Model_old {
	protected $dbo;
	
	function __construct() {
		$this->dbo = Configurator::getInstance()->getConnection('schoollog');
		$this->dbo = $this->dbo->getLink();
	}
	
	abstract function create($props);
	
	abstract function delete($i);
	
	abstract function get($props);
	
	abstract function update($i, $props);
}
?>
