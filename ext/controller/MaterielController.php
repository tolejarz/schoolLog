<?php
class MaterielController extends Controller {
	
	/* Fonction pour afficher la liste du matériel */
	function _doDefaultAction() {
		if ($_SESSION['user_privileges'] == 'superviseur' || $_SESSION['user_privileges'] == 'enseignant') {
			/* Récupération de l'id, du type et du modèle de tous les matériels dans la base */
			import('MaterielModel');
			$m = new MaterielModel($this->dbo);
			import('MaterielDefaultView');
			$v = new MaterielDefaultView();
			$v->show(array('equipments' => $m->listing()));
		}
	}
	
	/* Fonction pour supprimer un matériel */
	function _doDelete() {
		if ($_SESSION['user_privileges'] == 'superviseur') {
			import('MaterielModel');
			$m = new MaterielModel($this->dbo);
			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				if (isset($_POST['validation'])) {
					/*Suppression du matériel dans la base */
					$m->delete($this->_getArg('id'));
				}
				$this->redirect('EquipmentList');
			}
			/* Récupération des informations associées au matériel dans la base */
			$r = $m->get(array('id' => $this->_getArg('id')));
			import('MaterielDeleteView');
			$v = new MaterielDeleteView();
			$v->show(array('id' => $this->_getArg('id'), 'type' => $r['type'], 'modele' => $r['modele']));
		}
	}
	
	/* Fonction pour ajouter un matériel */
	function _doAdd() {
		if ($_SESSION['user_privileges'] == 'superviseur') {
			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				if (isset($_POST['annulation'])) {
					$this->redirect('EquipmentList');
				}
				
				/* Gestion des erreurs */
				if (empty($_POST['type'])) {
					$_SESSION['ERROR_MSG'] = 'Veuillez remplir le champ <b>Type de matériel</b>';
				} else if (empty($_POST['modele'])) {
					$_SESSION['ERROR_MSG'] = 'Veuillez remplir le champ <b>Modèle</b>';
				} else {
					if(($this->dbo->sqleval('select count(*) from materiels where type="' . $_POST['type'] . '" and modele="' . $_POST['modele'] . '"')) > 0) {
						$_SESSION['ERROR_MSG'] = 'Ce matériel existe déjà';
					}
				}
				/* Fin de la gestion des erreurs */
				
				if (!isset($_SESSION['ERROR_MSG'])) {
					if (isset($_POST['validation'])) {
						/* S'il n'y a pas d'erreurs, ajout du matériel dans la base */
						import('MaterielModel');
						$m = new MaterielModel($this->dbo);
						$m->create(array('type' => $_POST['type'], 'modele' => $_POST['modele']));
					}
					$this->redirect('EquipmentList');
				}
			}
			import('MaterielAddView');
			$v = new MaterielAddView();
			$v->show(array());
		}
	}
	
	/* Fonction pour éditer le type et le modèle d'un matériel */
	function _doEdit() {
		if ($_SESSION['user_privileges'] == 'superviseur') {
			/* Récupération des informations associées au matériel dans la base */
			import('MaterielModel');
			$m = new MaterielModel($this->dbo);
			$r = $m->get($this->_getArg('id'));
			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				if (isset($_POST['annulation'])) {
					$this->redirect('EquipmentList');
				}
				
				/* Gestion des erreurs */
				if (empty($_POST['type'])) {
					$_SESSION['ERROR_MSG'] = 'Veuillez remplir le champ <b>Type de matériel</b>';
				} else if (empty($_POST['modele'])) {
					$_SESSION['ERROR_MSG'] = 'Veuillez remplir le champ <b>Modèle</b>';
				} else {
					if(($this->dbo->sqleval('select count(*) from materiels where id!=' . $this->_getArg('id') . ' and type="' . $_POST['type'] . '" and modele="' . $_POST['modele'] . '"')) > 0) {
						$_SESSION['ERROR_MSG'] = 'Ce matériel existe déjà';
					}
				}
				/* Fin de la gestion des erreurs */
				
				if (!isset($_SESSION['ERROR_MSG'])) {
					if (isset($_POST['validation']) && ($_POST['type'] != $r['type'] || $_POST['modele'] != $r['modele']|| $_POST['etat'] != $r['etat'])) {
						/* S'il n'y a pas d'erreurs, et que le type de matériel ou le modèle est différent du précédent, mise à jour du matériel dans la base */
						$m->update($this->_getArg('id'), array('type' => $_POST['type'], 'modele' => $_POST['modele'], 'etat' => $_POST['etat']));
					}
					$this->redirect('EquipmentList');
				}
			}
			import('MaterielEditView');
			$params = array('id' => $this->_getArg('id'), 'type' => $r['type'], 'modele' => $r['modele'], 'etat' => $r['etat']);
			$v = new MaterielEditView();
			$v->show($params);		
		}
	}
}
?>
