<?php
class MySQLiDBO extends DBO {
	private $mysqli = null;
	
	function __construct($host, $username, $password, $database) {
		$this->mysqli = new mysqli($host, $username, $password, $database);
		$this->mysqli->set_charset('utf8');
	}
	
	function __destruct() {
		$this->mysqli->close();
	}
	
	function delete($query) {
		$this->mysqli->query($query);
		return $this->mysqli->affected_rows;
	}
	
	function insert($query) {
		$this->mysqli->query($query);
		return $this->mysqli->insert_id;
	}
	
	function query($query) {
		$q = $this->mysqli->query($query);
		$res = array();
		while ($r = $q->fetch_assoc()) {
			$res[] = $r;
		}
		return $res;
	}
	
	function singleQuery($query) {
		$r = $this->query($query . ' limit 0, 1');
		return $r[0];
	}
	
	function sqleval($query) {
		$q = $this->mysqli->query($query);
		$r = $q->fetch_row();
		return $r[0];
	}
	
	function update($query) {
		$this->mysqli->query($query);
		return $this->mysqli->affected_rows;
	}
}
?>
