<?php
class MatieresClasseController extends Controller {
	
	/* Fonction pour afficher la liste des matières et des enseignants associés pour une classe */	
	public function doList() {
		if ($_SESSION['user_privileges'] == 'superviseur') {
			/* Récupération de l'id et du libellé de toutes les classes dans la base */
			$m = new ClasseModel($this->dbo);
			$classes = $m->listing();
			
			/* Si aucune classe n'est passée en paramètres, on affiche les informations de la première classe disponible dans la base */
			$id_classe = $this->_getArg('id_classe');
			if (empty($id_classe)) {
				$id_classe = $classes[0]['id'];
			}
			
			$v = new MatieresClasseSelectView();
			$v->show(array('classes' => $classes, 'id_classe' => $id_classe));
			
			/* Récupération de l'id, du nom et de l'ensemble des enseignants (id, nom) des matières associées à la classe dans la base */
			$subjects = array();
			$resm = $this->dbo->query('select distinct m.id as id, m.nom as nom from enseignants_matieres_classes emc, matieres m where emc.id_matiere=m.id and emc.id_classe=' . $id_classe . ' order by nom asc');
			foreach ($resm as $rm) {
				$rese = $this->dbo->query('select u.id as id_enseignant, u.login as login, u.nom as nom,u.civility as civility, date_format(u.derniere_connexion, "le %d/%m/%Y à %Hh%i") as lastlog, u.charte_signee as charte_signee from utilisateurs u, enseignants_matieres_classes emc where emc.id_enseignant=u.id and emc.id_classe=' . $id_classe .' and emc.id_matiere=' . $rm['id'] .' and find_in_set("enseignant", u.droits) > 0 order by login asc');
				$enseignants = array();
				foreach ($rese as $rs) {
					$enseignants[] = array(
						'id' => $rs['id_enseignant'],
						'nom' => $rs['nom'],
						'civility' => $rs['civility']
					);
				}
				$subjects[] = array('id' => $rm['id'], 'nom' => $rm['nom'], 'enseignants' => $enseignants);
			}
			
			/* Récupération du libellé de la classe dans la base */
			$classe = $this->dbo->sqleval('select libelle from classes where id=' . $id_classe);
			
			$v = new MatieresClasseDefaultView();
			$v->show(array('subjects' => $subjects, 'id_classe' => $id_classe, 'classe' => $classe));
		}
	}
	
	/* Fonction pour supprimer une matière d'une classe (supprime également tous ses enseignants associés) */	
	public function doDelete($args) {
		$class_id = $args['class_id'];
		if ($_SESSION['user_privileges'] == 'superviseur') {
			$m = new MatieresClasseModel($this->dbo);
			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				if (isset($_POST['validation'])) {
					/* Suppression de toutes les associations matière - classe dans la base */
					$m->delete(array('id_classe' => $class_id, 'id_matiere' => $this->_getArg('id_matiere')));
				}
				Router::redirect('ClassSubjectList', array('class_id' => $class_id));
			}
			/* Récupération des informations associées à la matière de la classe dans la base */
			$r = $m->get(array('id_classe' => $class_id, 'id_matiere' => $this->_getArg('id_matiere')));
			$params = array(
				'id_classe' => $class_id,
				'id_matiere' => $this->_getArg('id_matiere'),
				'matiere' => $r['matiere'],
				'classe' => $r['classe']
			);
			$v = new MatieresClasseDeleteView();
			$v->show($params);
		}
	}
	
	/* Fonction pour ajouter une matière à une classe (choix du ou des enseignants associés (optionnel)  */	
	public function doAdd($args) {
		$class_id = $args['class_id'];
		if ($_SESSION['user_privileges'] == 'superviseur') {
			/* Récupération de l'id et du nom de tous les enseignants dans la base */
			$m = new UserModel($this->dbo);
			$enseignants = $m->listingEnseignants();
			
			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				if (isset($_POST['validation'])) {
					$m = new MatieresClasseModel($this->dbo);
					$nb_add = 0;
					/* Pour chaque enseignant coché on crée une entrée qui l'associe à la matière de la classe dans la base */
					foreach($enseignants as $e) {
						if(isset($_POST['e_'.$e['id']])) {
							$m->create(array('id_enseignant' => $e['id'], 'id_classe' => $class_id, 'id_matiere' => $_POST['id_matiere']));
							$nb_add++;
						}
					}
					/* Si on a ajouté aucun enseignant dans la matière de la classe, on ajoute la matière à la classe sans enseignant dans la base */
					if(empty($nb_add)) {
						$m->create(array('id_classe' => $class_id, 'id_matiere' => $_POST['id_matiere']));
					}
				}
				Router::redirect('ClassSubjectList', array('class_id' => $class_id));
			}
			
			/* Récupération de l'id et du nom de toutes les matières qui ne sont pas associées à la classe dans la base */
			$resm = $this->dbo->query('select distinct m.id as id, m.nom as nom from matieres m, enseignants_matieres_classes emc where m.nom not in (select m.nom from matieres m, enseignants_matieres_classes emc where emc.id_matiere=m.id and emc.id_classe=' . $class_id . ') order by nom asc');
			$subjects = array();
			foreach ($resm as $rm) {
				$subjects[] = array('id' => $rm['id'], 'nom' => $rm['nom']);
			}
			
			/* Récupération du libellé de la classe dans la base */
			$classe = $this->dbo->sqleval('select libelle from classes where id=' . $class_id);
			
			$params = array(
				'subjects' => $subjects,
				'enseignants' => $enseignants,
				'id_classe' => $class_id,
				'classe' => $classe
			);
			$v = new MatieresClasseAddView();
			$v->show($params);
		}
	}
	
	/* Fonction pour éditer les enseignants associés à une matière */
	public function doEdit($args) {
		$class_id = $args['class_id'];
		if ($_SESSION['user_privileges'] == 'superviseur') {
			/* Récupération des informations associées à la matière de la classe dans la base */
			$m = new MatieresClasseModel($this->dbo);
			$r = $m->get(array('id_classe' => $class_id, 'id_matiere' => $this->_getArg('id_matiere')));
			
			/* Récupération de l'id et du nom de tous les enseignants dans la base */
			$m = new UserModel($this->dbo);
			$enseignants = $m->listingEnseignants();
			
			/* Récupération de tous les id des enseignants associés à la matière de la classe dans la base */
			$resem = $this->dbo->query('select id_enseignant from enseignants_matieres_classes where id_classe=' . $class_id . ' and id_matiere='. $this->_getArg('id_matiere'));
			$enseignants_matiere = array();
			foreach ($resem as $rem) {
				$enseignants_matiere[] = $rem['id_enseignant'];
			}
			
			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				if (isset($_POST['validation'])) {
					$m = new MatieresClasseModel($this->dbo);
					$m->delete(array('id_classe' => $class_id, 'id_matiere' => $this->_getArg('id_matiere')));
					
					$hasEnseignants = false;
					foreach ($_POST as $key => $value) {
						if (substr($key, 0, 2) == 'e_') {
							$hasEnseignants = true;
							$m->create(array('id_enseignant' => substr($key, 2), 'id_classe' => $class_id, 'id_matiere' => $this->_getArg('id_matiere')));
						}
					}
					if (!$hasEnseignants) {
						$m->create(array('id_classe' => $class_id, 'id_matiere' => $this->_getArg('id_matiere')));
					}
				}
				Router::redirect('ClassSubjectList', array('class_id' => $class_id));
			}
			$params = array(
				'enseignants' 				=> $enseignants,
				'enseignants_matiere' 		=> $enseignants_matiere,
				'id_classe' 				=> $class_id,
				'id_matiere' 				=> $this->_getArg('id_matiere'),
				'classe' 					=> $r['classe'],
				'matiere' 					=> $r['matiere']
			);
			$v = new MatieresClasseEditView();
			$v->show($params);
		}
	}
}
?>
