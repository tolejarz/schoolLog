<?php
abstract class Model {
	protected $dbo;
	
	function __construct($dbo) {
		$this->dbo = $dbo;
	}
	
	abstract function create($props);
	
	abstract function delete($i);
	
	abstract function get($props);
	
	abstract function update($i, $props);
}
?>
