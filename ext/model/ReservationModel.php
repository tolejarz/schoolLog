<?php
class ReservationModel extends Model_old {
	function create($props) {
		$parms = array();
		if (array_key_exists('date_heure_debut', $props)) {
			$parms['date_heure_debut'] = "'" . $props['date_heure_debut'] . "'";
		}
		if (array_key_exists('date_heure_fin', $props)) {
			$parms['date_heure_fin'] = "'" . $props['date_heure_fin'] . "'";
		}
		if (array_key_exists('id_enseignant', $props)) {
			$parms['id_enseignant'] = $props['id_enseignant'];
		}
		if (array_key_exists('id_materiel', $props)) {
			$parms['id_materiel'] = $props['id_materiel'];
		}
		if (array_key_exists('etat', $props) && !empty($props['etat'])) {
			$parms['etat'] = "'" . $props['etat'] . "'";
		} else {
			$parms['etat'] = "'en attente'";
		}
		$fields = implode(',', array_keys($parms));
		$values = implode(',', array_values($parms));
		return $this->dbo->insert('insert into reservations(' . $fields . ') values(' . $values . ');');
	}
	
	function delete($id) {
		return $this->dbo->delete('delete from reservations where id=' . $id);
	}
	
	function get($props) {
		return $this->dbo->query('
			select 
				date_format(date_heure_debut, "%w") as jour,
				case date_format(date_heure_debut, "%w")
					when 1 then "lundi"
					when 2 then "mardi"
					when 3 then "mercredi"
					when 4 then "jeudi"
					when 5 then "vendredi"
				end as jour_libelle,
				unix_timestamp(date_heure_debut) as date_heure_debut,
				time(date_heure_debut) as heure_debut,
				addtime(time(date_heure_debut), timediff(date_heure_fin, date_heure_debut)) as heure_fin,
				r.id as id,
				r.etat as etat_reservation,
				m.etat as etat_materiel,
				concat(u.civility, " ", u.nom) as enseignant,
				u.id as id_enseignant
			from
				reservations r, materiels m, utilisateurs u
			where
				r.id_materiel=m.id
				and r.id_enseignant=u.id
				and date_format(date_heure_debut, "%u")=' . $props['week'] . '
				and r.id_materiel=' . $props['id_materiel']);
	}
	
	function getReservation($props) {
		return $this->dbo->singleQuery('select date(date_heure_debut) as date_reservation, time(date_heure_debut) as heure_debut, time(date_heure_fin) as heure_fin, concat(m.type, " ", m.modele) as materiel , concat(u.civility, " ", u.nom) as enseignant from reservations r, materiels m, utilisateurs u where r.id_materiel=m.id and r.id_enseignant=u.id and r.id=' . $props['id']); 
	}
	
	function update($i, $props) {
		return $this->dbo->update('update reservations set etat="' . $props['etat'] . '" where id=' . $i);
	}
}
?>
