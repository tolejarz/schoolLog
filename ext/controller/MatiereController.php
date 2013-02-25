<?php
class MatiereController extends Controller {

	/* Fonction pour afficher la liste des matières */
	function _doDefaultAction() {
		if ($_SESSION['user_privileges'] == 'superviseur') {
			/* Récupération de l'id et du nom de toutes les matières dans la base */
			import('MatiereModel');
			$m = new MatiereModel($this->dbo);
			import('MatiereDefaultView');
			$v = new MatiereDefaultView();
			$v->show(array('subjects' => $m->listing()));
		}
	}
	
	/* Fonction pour supprimer une matière */
	function _doDelete() {
		if ($_SESSION['user_privileges'] == 'superviseur') {
			import('MatiereModel');
			$m = new MatiereModel($this->dbo);
			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				if (isset($_POST['validation'])) {
					/*Suppression la matière dans la base */
					$m->delete($this->_getArg('id'));
				}
				$this->redirect('SubjectList');
			}
			/* Récupération des informations associées à la matière dans la base */
			$r = $m->get(array('id' => $this->_getArg('id')));
			import('MatiereDeleteView');
			$v = new MatiereDeleteView();
			$v->show(array('id' => $this->_getArg('id'), 'nom' => $r['nom']));
		}
	}
	
	/* Fonction pour ajouter une matière */
	function _doAdd() {
		if ($_SESSION['user_privileges'] == 'superviseur') {
			/* Récupération de l'id et du libelle de toutes les classes dans la base */
			import('ClasseModel');
			$m = new ClasseModel($this->dbo);
			$classes = $m->listing();
			
			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				if (isset($_POST['annulation'])) {
					$this->redirect('SubjectList');
				}
				
				/* Gestion des erreurs */
				if (empty($_POST['nom'])) {
					$_SESSION['ERROR_MSG'] = 'Veuillez remplir le champ <b>Nom</b>';
				} else {
					if($this->dbo->sqleval('select count(*) from matieres where nom="' . $_POST['nom'] . '"') > 0) {
						$_SESSION['ERROR_MSG'] = 'Cette matière existe déjà';
					}
				}
				/* Fin de la gestion des erreurs */
				
				if (!isset($_SESSION['ERROR_MSG'])) {
					if (isset($_POST['validation'])) {
						/* S'il n'y a pas d'erreurs, ajout de la matière dans la base */
						import('MatiereModel');
						$m = new MatiereModel($this->dbo);
						$id_matiere = $m->create(array('nom' => $_POST['nom']));
						/* Pour chaque classe cochée on crée une entrée qui l'associe à la matière dans la base */
						import('MatieresClasseModel');
						$m = new MatieresClasseModel($this->dbo);
						foreach($classes as $c)
						{
							if(isset($_POST['c_'.$c['id']])) {
								$m->create(array('id_classe' => $c['id'], 'id_matiere' => $id_matiere));
							}
						}
					}
					$this->redirect('SubjectList');
				}
			}
			import('MatiereAddView');
			$v = new MatiereAddView();
			$v->show(array('classes' => $classes));
		}
	}
	
	/* Fonction pour éditer le nom d'une matière */
	function _doEdit() {
		if ($_SESSION['user_privileges'] == 'superviseur') {
			/* Récupération des informations associées à la matière dans la base */
			import('MatiereModel');
			$m = new MatiereModel($this->dbo);
			$r = $m->get(array('id' => $this->_getArg('id')));
			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				if (isset($_POST['annulation'])) {
					$this->redirect('SubjectList');
				}
				
				/* Gestion des erreurs */
				if (empty($_POST['nom'])) {
					$_SESSION['ERROR_MSG'] = 'Veuillez remplir le champ <b>Nom</b>';
				} else {
					if($this->dbo->sqleval('select count(*) from matieres where id!=' . $this->_getArg('id') . ' and nom="' . $_POST['nom'] . '"') > 0) {
						$_SESSION['ERROR_MSG'] = 'Cette matière existe déjà';
					}
				}
				/* Fin de la gestion des erreurs */
				
				if (!isset($_SESSION['ERROR_MSG'])) {
					if (isset($_POST['validation']) && $_POST['nom'] != $r['nom']) {
						/* S'il n'y a pas d'erreurs, et que le nom est différent du précédent, mise à jour de la matière dans la base */
						$m->update($this->_getArg('id'), array('nom' => $_POST['nom']));
					}
					$this->redirect('SubjectList');
				}
			}
			import('MatiereEditView');
			$v = new MatiereEditView();
			$v->show(array('id' => $this->_getArg('id'), 'nom' => $r['nom']));
		}
	}
}
?>
