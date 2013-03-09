<?php
class MatieresClasseModel extends Model_old {
	function create($props) {
		$parms = array();
		if (array_key_exists('id_enseignant', $props)) {
			$parms['id_enseignant'] = "'" . $props['id_enseignant'] . "'";
		} else {
			$parms['id_enseignant'] = "null";
		}
		if (array_key_exists('id_matiere', $props)) {
			$parms['id_matiere'] = "'" . $props['id_matiere'] . "'";
		}
		if (array_key_exists('id_classe', $props)) {
			$parms['id_classe'] = "'" . $props['id_classe'] . "'";
		}
		$fields = implode(',', array_keys($parms));
		$values = implode(',', array_values($parms));
		return $this->dbo->insert('insert into enseignants_matieres_classes (' . $fields . ') values(' . $values . ');');
	}
	
	function delete($i) {
		$props = $i;
		$whereEnseignant = isset($props['id_enseignant']) ? ' and id_enseignant=' . $props['id_enseignant'] : '';
		return $this->dbo->delete('delete from enseignants_matieres_classes where id_classe=' . $props['id_classe'] . ' and id_matiere=' . $props['id_matiere'] . $whereEnseignant);
	}
	
	function get($props) {
		if(isset($props['id_enseignant'])) {
			return $this->dbo->singleQuery('select e.nom as enseignant, m.nom as matiere, c.libelle as classe from classes c, matieres m, utilisateurs e, enseignants_matieres_classes emc where emc.id_matiere=m.id and emc.id_classe=c.id and emc.id_enseignant=e.id and emc.id_classe=' . $props['id_classe'] . ' and emc.id_matiere=' . $props['id_matiere'] . ' and emc.id_enseignant=' . $props['id_enseignant']);
		}
		return $this->dbo->singleQuery('select distinct m.nom as matiere, c.libelle as classe from classes c, matieres m, enseignants_matieres_classes emc where emc.id_matiere=m.id and emc.id_classe=c.id and emc.id_classe=' . $props['id_classe'] . ' and emc.id_matiere=' . $props['id_matiere']);
	}
	
	function getClassesEnseignant($props) {
		return $this->dbo->query('select distinct c.id, c.libelle from classes c, enseignants_matieres_classes e where c.id=e.id_classe and e.id_enseignant=' . $props['id']);
	}
	
	function getSubjectsClass($props) {
		return $this->dbo->query('select distinct m.id, m.nom from matieres m, enseignants_matieres_classes emc where emc.id_matiere= m.id and emc.id_classe=' . $props['id'] . ' order by nom asc');
	}
	
	function getSubjectsEnseignant($props) {
		$subjects = $this->dbo->query('select id_classe, id_matiere from enseignants_matieres_classes where id_enseignant=' . $props['id']);
		$r = array();
		foreach ($subjects as $k => $v) {
			$r[$v['id_classe']][] = $v['id_matiere'];
		}
		return $r;
	}
	
	function update($i, $props) {}
}
?>
