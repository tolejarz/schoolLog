<?php
class UserModel extends Model {
	function auth($l, $p) {
		$r = $this->dbo->singleQuery("select id, nom, civility, email, date_format(derniere_connexion, 'le %d/%m/%Y à %Hh%i') as lastlog, charte_signee, droits from utilisateurs where login='" . $l . "'");
		
		$classes_subjects = NULL;
		/* On remplit les classes et matières si l'utilisateur a le droit "enseignant" */
		if ($r['droits'] == 'enseignant') {
			$rc = $this->dbo->query('select id_matiere, id_classe from enseignants_matieres_classes where id_enseignant=' . $r['id']);
			for ($i = 0; $i < count($rc); $i++) {
				$classes_subjects[$rc[$i]['id_classe']][] = $rc[$i]['id_matiere'];
			}
		}
		/* On remplit la classe si l'utilisateur a le droit "eleve" */
		if ($r['droits'] == 'eleve') {
			$rc = $this->dbo->singleQuery('select id_classe from eleves_classes where id_eleve=' . $r['id']);
		}
		$this->dbo->update('update utilisateurs set derniere_connexion=CURRENT_TIMESTAMP where id=' . $r['id']);
		$r = array(
			'auth' 						=> true,
			'error' 					=> null,
			'user_charter' 				=> $r['charte_signee'] == 1,
			'user_class' 				=> $r['droits'] == 'eleve' ? $rc['id_classe'] : null,
			'user_classes_subjects' 	=> $classes_subjects,
			'user_id' 					=> $r['id'],
			'user_login' 				=> $l,
			'user_civility' 			=> $r['civility'],
			'user_surname' 				=> 'prenom',
			'user_name' 				=> $r['nom'],
			'user_email' 				=> $r['email'],
			'user_lastlog' 				=> $r['lastlog'],
			'user_privileges' 			=> $r['droits']
		);
		return $r;
	}
	
	function create($props) {
		$parms = array();
		if (array_key_exists('login', $props)) {
			$parms['login'] = "'" . $props['login'] . "'";
		}
		if (array_key_exists('nom', $props)) {
			$parms['nom'] = "'" . $props['nom'] . "'";
		}
		if (array_key_exists('email', $props)) {
			$parms['email'] = "'" . $props['email'] . "'";
		}
		if (array_key_exists('droits', $props)) {
			$parms['droits'] = "'" . $props['droits'] . "'";
		}
		$fields = implode(',', array_keys($parms));
		$values = implode(',', array_values($parms));
		return $this->dbo->insert('insert into utilisateurs (' . $fields . ') values(' . $values . ');');
	}
	
	function createEleveClasse($props) {
		$parms = array();
		if (array_key_exists('id_eleve', $props)) {
			$parms['id_eleve'] = "'" . $props['id_eleve'] . "'";
		}
		if (array_key_exists('id_classe', $props)) {
			$parms['id_classe'] = "'" . $props['id_classe'] . "'";
		}
		$fields = implode(',', array_keys($parms));
		$values = implode(',', array_values($parms));
		return $this->dbo->insert('insert into eleves_classes (' . $fields . ') values(' . $values . ');');
	}
	
	function delete($i) {
		return $this->dbo->delete('delete from utilisateurs where id=' . $i);
	}
	
	function get($props) {
		return $this->dbo->singleQuery('select *, replace(droits, ",administrateur", "") droits, case when find_in_set("administrateur", droits) > 0 then 1 else 0 end as admin from utilisateurs where id=' . $props['id']);
	}
	
	function ldapAuth($l, $p) {
		$connect = ldapAuth(LDAP_SERVER, $l, $p);
		if (!empty($connect)) {
			$auth = true;
			$existe = $this->dbo->singleQuery("select id, charte_signee from utilisateurs where login='" . $l . "'");
			
			/* Si un élève n'existe pas dans la base on l'ajoute lors de sa première connexion */
			if(empty($existe)) {
				import('UserModel');
				$parms = array(
							'droits' 		=> $connect['droits'],
							'login' 		=> $l,
							'nom' 			=> $connect['nom'],
							'email' 		=> $connect['email']
						);
				$m = new UserModel($this->dbo);
				$id = $m->create($parms);
				if($connect['droits'] == 'eleve') {
					$id_classe = $this->dbo->sqlEval("select id from classes where libelle like '%" . $connect['classe'] . "%'");
					$m->createEleveClasse(array('id_eleve' => $id, 'id_classe' => $id_classe));
				}
			} else if(empty($existe['charte_signee'])) {
				$infosLDAP = recupererInfos(LDAP_SERVER,$l);
				$id = $this->dbo->sqlEval("select id from utilisateurs where login='" . $l . "'");
				import('UserModel');
				$parms = array(
							'droits' 		=> $infosLDAP['droits'],
							'login' 		=> $l,
							'nom' 			=> $infosLDAP['nom'],
							'email' 		=> $infosLDAP['email']
						);
				$m = new UserModel($this->dbo);
				$m->update($id, $parms);
			}
			$r = $this->dbo->singleQuery("select id, civility, nom, email, date_format(derniere_connexion, 'le %d/%m/%Y à %Hh%i') as lastlog, charte_signee, droits from utilisateurs where login='" . $l . "'");
			
			/* On remplit les classes et matières si l'utilisateur a le droit "enseignant" */
			if ($r['droits'] == 'enseignant') {
				$rc = $this->dbo->query('select id_matiere, id_classe from enseignants_matieres_classes where id_enseignant=' . $r['id']);
				for ($i = 0; $i < count($rc); $i++) {
					$classes_subjects[$rc[$i]['id_classe']][] = $rc[$i]['id_matiere'];
				}
			}
			/* On remplit la classe si l'utilisateur a le droit "eleve" */
			if ($r['droits'] == 'eleve') {
				$rc = $this->dbo->singleQuery('select id_classe from eleves_classes where id_eleve=' . $r['id']);
			}
			$this->dbo->update('update utilisateurs set derniere_connexion=CURRENT_TIMESTAMP where id=' . $r['id']);
			$result = array(
				'auth' 							=> $auth,
				'user_charter' 					=> $r['charte_signee'] == 1,
				'user_class' 					=> ($r['droits'] == 'eleve' ? $rc['id_classe'] : null),
				'user_classes_subjects' 		=> (isset($classes_subjects) ? $classes_subjects : null),
				'user_id' 						=> $r['id'],
				'user_login' 					=> $l,
				'user_civility' 				=> $r['civility'],
				'user_surname' 					=> $connect['prenom'],
				'user_name' 					=> $r['nom'],
				'user_email' 					=> $r['email'],
				'user_lastlog' 					=> $r['lastlog'],
				'user_privileges' 				=> $r['droits']
			);
			return $result;
		}
		else {
			$infosLDAP = recupererInfos(LDAP_SERVER, $l);
			return !empty($infosLDAP['error_connex']) ? 'CONNECTION_FAILED' : 'BAD_LOGIN_MDP';
		}
	}
	
	function listing() {
		return $this->dbo->query('select id, nom, replace(droits, ",administrateur", "") droits, case when find_in_set("administrateur", droits) > 0 then 1 else 0 end as administrateur from utilisateurs order by droits asc, nom asc');
	}
	
	function listingEnseignants() {
		return $this->dbo->query('select id, nom, civility from utilisateurs where find_in_set("enseignant", droits) > 0 order by nom asc');
	}
	
	function update($i, $props) {
		if (isset($props['login'])) {
			$parms[] = "login='" . $props['login'] . "'";
		}
		if (isset($props['nom'])) {
			$parms[] = "nom='" . $props['nom'] . "'";
		}
		if (isset($props['email'])) {
			$parms[] = "email='" . $props['email'] . "'";
		}
		if (isset($props['droits'])) {
			$parms[] = "droits='" . $props['droits'] . "'";
		}
		if (isset($props['charte_signee'])) {
			$parms[] = 'charte_signee=' . ($props['charte_signee'] == true ? 1 : 0);
		}
		return $this->dbo->update('update utilisateurs set ' . implode(',', array_values($parms)) . ' where id=' . $i);
	}
}
?>
