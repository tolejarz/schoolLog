<?php
abstract class DBO {
	abstract function delete($query);
	
	abstract function insert($query);
	
	abstract function query($query);
	
	abstract function update($query);
}
?>
