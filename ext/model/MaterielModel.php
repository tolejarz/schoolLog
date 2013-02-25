<?php
class MaterielModel extends Model {
	function create($props) {
		$parms = array();
		if (array_key_exists('type', $props)) {
			$parms['type'] = "'" . $props['type'] . "'";
		}
		if (array_key_exists('modele', $props)) {
			$parms['modele'] = "'" . $props['modele'] . "'";
		}
		$fields = implode(',', array_keys($parms));
		$values = implode(',', array_values($parms));
		return $this->dbo->insert('insert into materiels(' . $fields . ') values(' . $values . ');');
	}
	
	function delete($id) {
		return $this->dbo->delete('delete from materiels where id=' . $id);
	}
	
	function get($props) {
		return $this->dbo->singleQuery('select * from materiels where id=' . $props['id']);
	}
	
	function listing() {
		return $this->dbo->query('select * from materiels order by type asc, modele asc');
	}
	
	function listingFonctionnels() {
		return $this->dbo->query('select * from materiels where etat!="en maintenance" order by type asc, modele asc');
	}
	
	function update($id, $props) {
		$parms = array();
		if (array_key_exists('type', $props)) {
			$parms[] = "type='" . $props['type'] . "'";
		}
		if (array_key_exists('modele', $props)) {
			$parms[] = "modele='" . $props['modele'] . "'";
		}
		if (array_key_exists('etat', $props)) {
			$parms[] = "etat='" . $props['etat'] . "'";
		}
		return $this->dbo->update('update materiels set ' . implode(',', $parms) . ' where id=' . $id);
	}
}
?>
