<?php
class ClasseModel extends Model_old {
	function create($props) {
		$parms = array();
		if (array_key_exists('libelle', $props)) {
			$parms['libelle'] = "'" . $props['libelle'] . "'";
		}
		if (array_key_exists('email', $props)) {
			$parms['email'] = "'" . $props['email'] . "'";
		}
		$fields = implode(',', array_keys($parms));
		$values = implode(',', array_values($parms));
		return $this->dbo->insert('insert into classes (' . $fields . ') values(' . $values . ');');
	}
	
	function delete($id) {
		return $this->dbo->delete('delete from classes where id=' . $id);
	}
	
	function get($props) {
		return $this->dbo->singleQuery('select * from classes where id=' . $props['id']);
	}
	
	function listing() {
		return $this->dbo->query('select * from classes order by libelle asc');
	}
	
	function update($id, $props) {
		$parms = array();
		if (array_key_exists('libelle', $props)) {
			$parms[] = "libelle='" . $props['libelle'] . "'";
		}
		if (array_key_exists('email', $props)) {
			$parms[] = "email='" . $props['email'] . "'";
		}
		return $this->dbo->update('update classes set ' . implode(',', $parms) . ' where id=' . $id);
	}
}
?>
