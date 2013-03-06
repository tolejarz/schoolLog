<?php
class SupportModel extends Model {
	function create($props) {
		$parms = array();
		if (array_key_exists('titre', $props)) {
			$parms['titre'] = "'" . $props['titre'] . "'";
		}
		if (array_key_exists('nom_fichier', $props)) {
			$parms['nom_fichier'] = "'" . $props['nom_fichier'] . "'";
		}
		if (array_key_exists('tags', $props)) {
			$parms['tags'] = "'" . $props['tags'] . "'";
		}
		if (array_key_exists('id_enseignant', $props)) {
			$parms['id_enseignant'] = $props['id_enseignant'];
		}
		if (array_key_exists('id_matiere', $props)) {
			$parms['id_matiere'] = $props['id_matiere'];
		}
		if (array_key_exists('id_classe', $props)) {
			$parms['id_classe'] = $props['id_classe'];
		}
		$fields = implode(',', array_keys($parms));
		$values = implode(',', array_values($parms));
		return $this->dbo->insert('insert into supports(' . $fields . ') values(' . $values . ')');
	}
	
	function delete($i) {
		$filename = $this->dbo->sqleval('select nom_fichier from supports where id=' . $i);
		$f = new FileManipulation();
		$f->delete(UPLOAD_PATH . $filename);
		return $this->dbo->delete('delete from supports where id=' . $i);
	}
	
	function get($props) {
		return $this->dbo->singleQuery('select s.titre, s.nom_fichier, s.tags, m.nom as matiere, c.libelle as classe from supports s, classes c, matieres m where s.id_matiere=m.id and s.id_classe=c.id and s.id=' . $props['id']);
	}
	
	function listing($id_classe = '', $id_matiere = '') {
		return $this->dbo->query('select s.id, date_format(s.date_creation, "%d/%m/%Y %H:%i") as date, s.titre, s.nom_fichier, s.tags, concat(u.civility, " ", u.nom) as enseignant from supports s, utilisateurs u where s.id_enseignant=u.id' . (!empty($id_classe) ? ' and s.id_classe=' . $id_classe . ' ' . (!empty($id_matiere) ? ' and s.id_matiere=' . $id_matiere : '') : '') . ' order by s.date_creation desc');
	}
	
	function update($i, $props) {
		$parms = array();
		if (array_key_exists('titre', $props)) {
			$parms[] = "titre='" . $props['titre'] . "'";
		}
		if (array_key_exists('nom_fichier', $props)) {
			$parms[] = "nom_fichier='" . $props['nom_fichier'] . "'";
		}
		if (array_key_exists('tags', $props)) {
			$parms[] = "tags='" . $props['tags'] . "'";
		}
		if (array_key_exists('id_enseignant', $props)) {
			$parms[] = "id_enseignant=" . $props['id_enseignant'];
		}
		if (array_key_exists('id_matiere', $props)) {
			$parms[] = "id_matiere=" . $props['id_matiere'];
		}
		if (array_key_exists('id_classe', $props)) {
			$parms[] = "id_classe=" . $props['id_classe'];
		}
		return $this->dbo->update('update supports set ' . implode(',', $parms) . ' where id=' . $i);
	}
}
?>
