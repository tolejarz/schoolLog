<?php
class CalendrierModel extends Model_old {

	function get($props) {
		$whereEnseignant = $whereEleve = $whereClasse = '';
		if (isset($props['id_enseignant'])) {
			$whereEnseignant = ' and mp.id_enseignant=' . $props['id_enseignant'];
		} else if (isset($props['id_eleve'])) {
			$whereClasse = ' and mp.id_classe=(select id_classe from eleves_classes where id_eleve=' . $props['id_eleve'] . ')';
		} else if (isset($props['id_classe'])) {
			$whereClasse = ' and mp.id_classe=' . $props['id_classe'];
		}
		
		if ($props['viewer_type'] == 'superviseur') {
			$demands = '';
		} else if ($props['viewer_type'] == 'enseignant') {
			$demands = 'and (mp.id_enseignant=' . $_SESSION['user_id'] . ' or o.etat="validée")';
		} else {
			$demands = 'and o.etat="validée"';
		}
		
		$reported = $this->dbo->query('
			select 
				date_origine as new,
				o.etat,
				mp.id,
				o.id as id_operation,
				date_format(date_report, "%w") - 1 as jour,
				case date_format(date_report, "%w")
					when 0 then "dimanche"
					when 1 then "lundi"
					when 2 then "mardi"
					when 3 then "mercredi"
					when 4 then "jeudi"
					when 5 then "vendredi"
					when 6 then "samedi"
				end as jour_libelle,
				time(date_report) as heure_debut,
				addtime(time(date_report), timediff(mp.heure_fin, mp.heure_debut)) as heure_fin,
				m.nom as matiere,
				m.id as id_matiere,
				c.libelle as classe,
				c.id as id_classe,
				concat(u.civility, " ", u.nom) as enseignant,
				u.id as id_enseignant
			from
				operations o, matieres m, classes c, modele_planning mp
			left join utilisateurs u on u.id=mp.id_enseignant
			where
				o.id_modele_planning=mp.id
				' . $demands . '
				and o.etat!="refusée"
				and mp.id_classe=c.id
				and mp.id_matiere=m.id
				and (o.date_origine between "' . date('Y-m-d', $props['start']) . '" and "' . date('Y-m-d', $props['end']) . '")' . 
				$whereEnseignant . 
				$whereClasse
		);
		
		$normal = $this->dbo->query('
			select 
				mp.id,
				mp.jour-2 as jour,
				mp.jour as jour_libelle,
				mp.heure_debut,
				mp.heure_fin,
				m.nom as matiere,
				m.id as id_matiere,
				c.libelle as classe,
				c.id as id_classe,
				concat(u.civility, " ", u.nom) as enseignant,
				u.id as id_enseignant
			from 
				matieres m, classes c, periodes p, modele_planning mp
			left join utilisateurs u on u.id=mp.id_enseignant
			where 
				not exists (select o.id_modele_planning from operations o where o.id_modele_planning=mp.id ' . $demands . ' and o.etat!="refusée" and (o.date_origine between "' . date('Y-m-d', $props['start']) . '" and "' . date('Y-m-d', $props['end']) . '"))
				and mp.id_periode=p.id
				and (date_add("' . date('Y-m-d', $props['start']) . '", INTERVAL (mp.jour - 2) DAY) between p.date_debut and p.date_fin)
				and not exists (select id from periodes where type="vacances" and (date_add("' . date('Y-m-d', $props['start']) . '", INTERVAL (mp.jour - 2) DAY) between date_debut and date_fin))
				and mp.id_classe=c.id
				and m.id=mp.id_matiere' . 
				$whereEnseignant . 
				$whereClasse
		);
		$holidays = $this->dbo->query('
			select
				id,
				unix_timestamp(date_debut) as date_debut_timestamp,
				unix_timestamp(date_fin) as date_fin_timestamp,
				date_format(date_debut, "%d/%m/%Y") as date_debut_f,
				date_format(date_fin, "%d/%m/%Y") as date_fin_f
			from
				periodes
			where
				type="vacances"
				and (
					date_debut between "' . date('Y-m-d', $props['start']) . '" and "' . date('Y-m-d', $props['end']) . '"
					or
					date_fin between "' . date('Y-m-d', $props['start']) . '" and "' . date('Y-m-d', $props['end']) . '"
					or
					(date_debut <= "' . date('Y-m-d', $props['start']) . '" and date_fin >= "' . date('Y-m-d', $props['end']) . '")
				)'
		);
		
		$days = array('dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi');
		$h = array();
		$d = $props['start'];
		if (!empty($holidays)) {
			$d = $props['start'];
			while (date('Ymd', $d) <= date('Ymd', $props['end'])) {
				if ((date('Ymd', $d) >= date('Ymd', $holidays[0]['date_debut_timestamp'])) && (date('Ymd', $d) <= date('Ymd', $holidays[0]['date_fin_timestamp']))) {
					$h[] = array(
						'jour_libelle'		=> $days[date('w', $d)],
						'jour'				=> date('w', $d),
						'heure_debut' 		=> '08:30:00',
						'heure_fin' 		=> '21:00:00',
						'holidays' 			=> true
					);
				}
				$d = strtotime(date('Y-m-d', $d) . ' +1 day');
			}
		}
		$r = array_merge($normal, $reported, $h);
		return $r;
	}
	
	function create($props) {}
	
	function delete($id) {}
	
	function update($id, $props) {}
	
	function getCours($props) {
		return $this->dbo->sqleval(	'
				select
					mp.id
				from
					modele_planning mp, periodes p
				where
					date_format("' . $props['date_origine'] . '", "%w")=(mp.jour-1)
					and mp.id_periode=p.id
					and "' . $props['date_origine'] . '" between p.date_debut and p.date_fin
					and p.type="cours"
					and mp.heure_debut="' . $props['heure_origine'] . '"
					and mp.id_classe=' . $props['id_classe'] . 
					(isset($props['id_enseignant']) ? ' and ' . $props['id_enseignant'] . ' in (select id_enseignant from enseignants_matieres_classes where id_matiere=mp.id_matiere and id_classe=mp.id_classe)': ''));
	}
	
	/* opérations */
	function createOperation($props) {
		$parms = array();
		if (array_key_exists('date_origine', $props)) {
			$parms['date_origine'] = "'" . $props['date_origine'] . "'";
		} else {
			$parms['date_origine'] = 'null';
		}
		if (array_key_exists('date_report', $props)) {
			$heure_origine = $this->dbo->sqleval('select heure_debut from modele_planning where id=' . $props['id_modele_planning']);
			if ($props['date_report'] . $props['heure_report'] == $props['date_origine'] . $heure_origine) {
				return null;
			}
			$parms['date_report'] = $props['date_report'] != null ? "'" . $props['date_report'] . ' ' . $props['heure_report'] . "'" : 'null';
		} else {
			$parms['date_report'] = 'null';
		}
		if (array_key_exists('id_enseignant', $props)) {
			$parms['id_enseignant'] = $props['id_enseignant'];
		}
		if (array_key_exists('id_modele_planning', $props)) {
			$parms['id_modele_planning'] = $props['id_modele_planning'];
		}
		if (array_key_exists('etat', $props)) {
			$parms['etat'] = "'" . $props['etat'] . "'";
		} else {
			$parms['etat'] = "'en attente'";
		}
		
		$fields = implode(',', array_keys($parms));
		$values = implode(',', array_values($parms));
		return $this->dbo->insert('insert into operations(' . $fields . ') values(' . $values . ')');
	}
	
	function deleteOperation($i) {
		return $this->dbo->delete('delete from operations where id=' . $i);
	}
	
	function getOperation($props) {
		return $this->dbo->singleQuery('select c.libelle as classe, ma.nom as matiere, o.date_origine, m.heure_debut as heure_origine, o.date_report, o.etat, o.id, concat(u.civility, " ", u.nom) as enseignant, c.email as classe_email, u.email as enseignant_email from modele_planning m, operations o, classes c, matieres ma, utilisateurs u where ma.id=m.id_matiere and o.id_modele_planning=m.id and c.id=m.id_classe and u.id=o.id_enseignant and o.id=' . $props['id']);
	}
	
	function listingOperations($i) {
		return $this->dbo->query('
			select 
				c.libelle as classe,
				m.nom as matiere,
				o.date_origine,
				mp.heure_debut,
				o.date_report,
				o.etat,
				o.id,
				concat(u.civility, " ", u.nom) as enseignant
			from
				modele_planning mp,
				operations o,
				classes c,
				matieres m,
				utilisateurs u
			where
				m.id=mp.id_matiere
				and o.id_modele_planning=mp.id
				and c.id=mp.id_classe
				and u.id=o.id_enseignant
				and (o.date_report is null or o.date_report>now())
				' . (!empty($i['id_enseignant']) ? 'and ' . $i['id_enseignant'] . ' in (select emc.id_enseignant from enseignants_matieres_classes emc where emc.id_matiere=mp.id_matiere and emc.id_classe=mp.id_classe)' : '') . '
			order by
				o.date_creation desc');
	}
	
	function updateOperation($id, $props) {
		$r = $this->dbo->query("select o.id from operations o, modele_planning mp where o.date_origine='" . $props['date_report'] . "' and mp.heure_debut='" . $props['heure_report'] . "' and o.id_modele_planning=mp.id and o.id=" . $id);
		
		// mise à jour
		if (empty($r)) {
			$parms = array();
			if (array_key_exists('etat', $props)) {
				$parms["etat"] = "etat='" . $props['etat'] . "'";
			}
			if (array_key_exists('date_report', $props)) {
				$parms['date_report'] = "date_report='" . $props['date_report'] . " " . $props['heure_report'] . "'";
			}
			
			return $this->dbo->update('update operations set ' . implode(',', $parms) . ' where id=' . $id);
		// suppression
		} else {
			$this->deleteOperation($id);
			return null;
		}
	}
	
	/* périodes */
	function deletePeriod($id) {
		return $this->dbo->delete('delete from periodes where id=' . $id);
	}
	
	function getPeriod($id) {
		return $this->dbo->singleQuery('select p.id, p.type, p.date_debut, p.date_fin, p.id_classe, c.libelle as classe from periodes p, classes c where p.id_classe=c.id and p.id=' . $id);
	}
	
	function getPeriods($id) {
		return $this->dbo->query('select * from periodes where id_classe=' . $id);
	}
	
	function createPeriod($props) {
		$parms = array();
		if (array_key_exists('type', $props)) {
			$parms['type'] = "'" . $props['type'] . "'";
		}
		if (array_key_exists('date_debut', $props)) {
			$parms['date_debut'] = "'" . $props['date_debut'] . "'";
		}
		if (array_key_exists('date_fin', $props)) {
			$parms['date_fin'] = "'" . $props['date_fin'] . "'";
		}
		if (array_key_exists('id_classe', $props)) {
			$parms['id_classe'] = $props['id_classe'];
		}
		$fields = implode(',', array_keys($parms));
		$values = implode(',', array_values($parms));
		return $this->dbo->insert('insert into periodes(' . $fields . ') values(' . $values . ');');
	}
	
	function updatePeriod($id, $props) {
		$parms = array();
		if (array_key_exists('type', $props)) {
			$parms[] = "type='" . $props['type'] . "'";
		}
		if (array_key_exists('date_debut', $props)) {
			$parms[] = "date_debut='" . $props['date_debut'] . "'";
		}
		if (array_key_exists('date_fin', $props)) {
			$parms[] = "date_fin='" . $props['date_fin'] . "'";
		}
		if (array_key_exists('id_classe', $props)) {
			$parms[] = 'id_classe=' . $props['id_classe'];
		}
		return $this->dbo->update('update periodes set ' . implode(',', $parms) . ' where id=' . $id);
	}
	
}
?>
