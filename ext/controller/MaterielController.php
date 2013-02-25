<?php
class MaterielController extends Controller {
	
	/* Fonction pour afficher la liste du mat�riel */
	function _doDefaultAction() {
		if ($_SESSION['user_privileges'] == 'superviseur' || $_SESSION['user_privileges'] == 'enseignant') {
			/* R�cup�ration de l'id, du type et du mod�le de tous les mat�riels dans la base */
			import('MaterielModel');
			$m = new MaterielModel($this->dbo);
			import('MaterielDefaultView');
			$v = new MaterielDefaultView();
			$v->show(array('equipments' => $m->listing()));
		}
	}
	
	/* Fonction pour supprimer un mat�riel */
	function _doDelete() {
		if ($_SESSION['user_privileges'] == 'superviseur') {
			import('MaterielModel');
			$m = new MaterielModel($this->dbo);
			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				if (isset($_POST['validation'])) {
					/*Suppression du mat�riel dans la base */
					$m->delete($this->_getArg('id'));
				}
				$this->redirect('EquipmentList');
			}
			/* R�cup�ration des informations associ�es au mat�riel dans la base */
			$r = $m->get(array('id' => $this->_getArg('id')));
			import('MaterielDeleteView');
			$v = new MaterielDeleteView();
			$v->show(array('id' => $this->_getArg('id'), 'type' => $r['type'], 'modele' => $r['modele']));
		}
	}
	
	/* Fonction pour ajouter un mat�riel */
	function _doAdd() {
		if ($_SESSION['user_privileges'] == 'superviseur') {
			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				if (isset($_POST['annulation'])) {
					$this->redirect('EquipmentList');
				}
				
				/* Gestion des erreurs */
				if (empty($_POST['type'])) {
					$_SESSION['ERROR_MSG'] = 'Veuillez remplir le champ <b>Type de mat�riel</b>';
				} else if (empty($_POST['modele'])) {
					$_SESSION['ERROR_MSG'] = 'Veuillez remplir le champ <b>Mod�le</b>';
				} else {
					if(($this->dbo->sqleval('select count(*) from materiels where type="' . $_POST['type'] . '" and modele="' . $_POST['modele'] . '"')) > 0) {
						$_SESSION['ERROR_MSG'] = 'Ce mat�riel existe d�j�';
					}
				}
				/* Fin de la gestion des erreurs */
				
				if (!isset($_SESSION['ERROR_MSG'])) {
					if (isset($_POST['validation'])) {
						/* S'il n'y a pas d'erreurs, ajout du mat�riel dans la base */
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
	
	/* Fonction pour �diter le type et le mod�le d'un mat�riel */
	function _doEdit() {
		if ($_SESSION['user_privileges'] == 'superviseur') {
			/* R�cup�ration des informations associ�es au mat�riel dans la base */
			import('MaterielModel');
			$m = new MaterielModel($this->dbo);
			$r = $m->get($this->_getArg('id'));
			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				if (isset($_POST['annulation'])) {
					$this->redirect('EquipmentList');
				}
				
				/* Gestion des erreurs */
				if (empty($_POST['type'])) {
					$_SESSION['ERROR_MSG'] = 'Veuillez remplir le champ <b>Type de mat�riel</b>';
				} else if (empty($_POST['modele'])) {
					$_SESSION['ERROR_MSG'] = 'Veuillez remplir le champ <b>Mod�le</b>';
				} else {
					if(($this->dbo->sqleval('select count(*) from materiels where id!=' . $this->_getArg('id') . ' and type="' . $_POST['type'] . '" and modele="' . $_POST['modele'] . '"')) > 0) {
						$_SESSION['ERROR_MSG'] = 'Ce mat�riel existe d�j�';
					}
				}
				/* Fin de la gestion des erreurs */
				
				if (!isset($_SESSION['ERROR_MSG'])) {
					if (isset($_POST['validation']) && ($_POST['type'] != $r['type'] || $_POST['modele'] != $r['modele']|| $_POST['etat'] != $r['etat'])) {
						/* S'il n'y a pas d'erreurs, et que le type de mat�riel ou le mod�le est diff�rent du pr�c�dent, mise � jour du mat�riel dans la base */
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
