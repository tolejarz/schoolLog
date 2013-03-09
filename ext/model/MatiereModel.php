<?php
class MatiereModel extends Model_old {
	function create($props) {
		return $this->dbo->insert('insert into matieres (nom) values("' . $props['nom'] . '");');
	}
	
	function delete($id) {
		return $this->dbo->delete('delete from matieres where id=' . $id);
	}
	
	function get($props) {
		return $this->dbo->singleQuery('select * from matieres where id=' . $props['id']);
	}
	
	function listing($id = '') {
		return $this->dbo->query('select * from matieres ' . (!empty($id) ? 'and id_classe=' . $id . ' ' : '') . 'order by nom asc');
	}
	
	function update($id, $props) {
		return $this->dbo->update('update matieres set nom="' . $props['nom'] . '" where id=' . $id);
	}
}
?>
