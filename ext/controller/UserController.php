<?php
class UserController extends Controller {
	/* Fonction pour afficher la liste des enseignants */
	function _doDefaultAction() {
		if (in_array($_SESSION['user_privileges'], array('superviseur', 'administrateur'))) {
			/* récupération de la liste des enseignants dans la base de données */
			import('UserModel');
			import('UserDefaultView');
			
			$m = new UserModel($this->dbo);
			$v = new UserDefaultView();
			$v->show(array('enseignants' => $m->listingEnseignants()));
		}
	}
	
	/* Fonction pour supprimer un enseignant */
	function _doDelete() {
		if (in_array($_SESSION['user_privileges'], array('superviseur', 'administrateur'))) {
			import('UserModel');
			$m = new UserModel($this->dbo);
			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				if (isset($_POST['validation'])) {
					/* Suppression de l'enseignant */
					$m->delete($this->_getArg('id'));
				}
				$this->redirect('UserList');
			}
			/* Récupération des informations de l'enseignant */
			import('UserDeleteView');
			$r = $m->get(array('id' => $this->_getArg('id')));
			$v = new UserDeleteView();
			$v->show($r);
		}
	}
	
	/* Fonction pour ajouter un enseignant */
	function _doAdd(){
		if (in_array($_SESSION['user_privileges'], array('superviseur', 'administrateur'))) {
			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				if (!isset($_POST['validation'])) {
					$this->redirect('UserList');
				}
				
				$login = strtolower($_POST['nom']);
				if (empty($login)) {
					$_SESSION['ERROR_MSG'] = 'Veuillez remplir le champ Nom';
				} else if ($this->dbo->sqleval('select count(*) from utilisateurs where login="' . $login . '"') > 0) {
					$_SESSION['ERROR_MSG'] = 'Cet utilisateur existe déjà';
				} else {
					$infosLDAP = recupererInfos(LDAP_SERVER, $login);
					if (empty($infosLDAP)) {
						$_SESSION['ERROR_MSG'] = 'Cet utilisateur n\'existe pas dans le serveur LDAP';
					} else {
						import('UserModel');
						$parms = array(
							'droits' 		=> 'enseignant',
							'login' 		=> $login,
							'civility' 		=> $_POST['civility'],
							'nom' 			=> $_POST['nom'],
							'email' 		=> $infosLDAP['email']
						);
						
						$m = new UserModel($this->dbo);
						$id = $m->create($parms);
						
						import('MatieresClasseModel');
						$m = new MatieresClasseModel($this->dbo);
						foreach ($_POST as $key => $value) {
							if (substr($key, 0, 3) == 'cm_') {
								$ids = explode('_', substr($key, 3));
								$m->create(array('id_enseignant' => $id, 'id_classe' => $ids[0], 'id_matiere' => $ids[1]));
							}
						}
					}
					$this->redirect('UserList');
				}
			}
			
			/* récupération des classes / matières */
			import('ClasseModel');
			$m = new ClasseModel($this->dbo);
			$resc = $m->listing();
			import('MatieresClasseModel');
			$m = new MatieresClasseModel($this->dbo);
			$classes = array();
			foreach ($resc as $c) {
				$matieres =  $m->getSubjectsClass(array('id' => $c['id']));
				$classes[] = array('id' => $c['id'], 'libelle' => $c['libelle'], 'matieres' => $matieres);
			}
			import('UserAddView');
			$v = new UserAddView();
			$v->show(array('classes' => $classes));
		}
	}
	
	/* Fonction pour éditer un enseignant */
	function _doEdit() {
		if (in_array($_SESSION['user_privileges'], array('superviseur', 'administrateur'))) {
			import('UserModel');
			$m = new UserModel($this->dbo);
			$r = $m->get(array('id' => $this->_getArg('id')));
			
			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				if (isset($_POST['annulation'])) {
					$this->redirect('UserList');
				}
				/* Gestion des erreurs */
				if (empty($_POST['nom'])) {
					$_SESSION['ERROR_MSG'] = 'Veuillez remplir le champ Nom';
				} elseif ($this->dbo->sqleval('select count(*) from utilisateurs where id!=' . $this->_getArg('id') . ' and login="' . $login . '" and id!=' . $this->_getArg('id')) > 0) {
					$_SESSION['ERROR_MSG'] = 'Cet utilisateur existe déjà';
				}
				/* Fin de la gestion des erreurs */
				
				if (!isset($_SESSION['ERROR_MSG'])) {
					if (isset($_POST['validation']) && ($_POST['nom'] != $r['nom'] || $_POST['civility'] != $r['civility'])) {
						$parms = array(
							'nom' 			=> $_POST['nom'],
							'civility' 		=> $_POST['civility'],
						);
						$m->update($this->_getArg('id'), $parms);
					}
					
					import('MatieresClasseModel');
					$r = $this->dbo->delete('delete from enseignants_matieres_classes where id_enseignant=' . $this->_getArg('id'));
					$m = new MatieresClasseModel($this->dbo);
					foreach ($_POST as $key => $value) {
						if (substr($key, 0, 3) == 'cm_') {
							$ids = explode('_', substr($key, 3));
							$m->create(array('id_enseignant' => $this->_getArg('id'), 'id_classe' => $ids[0], 'id_matiere' => $ids[1]));
						}
					}
					$this->redirect('UserList');
				}
			}
			
			/* récupération des matières de chaque classe */
			import('ClasseModel');
			$m = new ClasseModel($this->dbo);
			$resc = $m->listing();
			import('MatieresClasseModel');
			$m = new MatieresClasseModel($this->dbo);
			$classes = array();
			foreach ($resc as $c) {
				$matieres =  $m->getSubjectsClass(array('id' => $c['id']));
				$classes[] = array('id' => $c['id'], 'libelle' => $c['libelle'], 'matieres' => $matieres);
			}
			
			/* récupération des matières de l'enseignant sélectionné */
			import('MatieresClasseModel');
			$m = new MatieresClasseModel($this->dbo);
			$matieres_enseignant = $m->getSubjectsEnseignant(array('id' => $this->_getArg('id')));
			$params = array(
				'id' 					=> $this->_getArg('id'),
				'login' 				=> $r['login'],
				'nom' 					=> $r['nom'],
				'civility' 				=> $r['civility'],
				'classes' 				=> $classes,
				'matieres_enseignant'	=> $matieres_enseignant
			);
			import('UserEditView');
			$v = new UserEditView();
			$v->show($params);
		}
	}
}
?>
