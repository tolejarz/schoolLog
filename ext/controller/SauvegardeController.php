<?php
class SauvegardeController extends Controller {
	
	/* Fonction pour afficher la liste du matériel */
	function _doDefaultAction() {
		if ($_SESSION['user_privileges'] == 'administrateur') {
			$dir = opendir(SAVESQL_PATH);
			
			if($dir) {
				$sauvegardes = array();
				while (false !== ($f = readdir($dir))) {
					if (is_file(SAVESQL_PATH . $f)) {
						$fichier = explode (".", $f);
						if ($fichier[1] == "sql") {
							$date_heure = explode('_', $f);
							$heure = str_replace('-',':',$date_heure[1]);
							$sauvegardes[] = array('fichier' => $f, 'date' => $date_heure[0], 'heure' => $heure);
						}
					}
				}
				closedir($dir);
			}
			
			import('SauvegardeDefaultView');
			$v = new SauvegardeDefaultView();
			$v->show(array('sauvegardes' => $sauvegardes));
		}
	}
	
	/* Fonction pour supprimer une sauvegarde */
	function _doDelete() {
		if ($_SESSION['user_privileges'] == 'administrateur') {
			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				if (isset($_POST['validation'])) {
					/*Suppression de la sauvegarde sur le serveur */
					unlink (SAVESQL_PATH . $this->_getArg('fichier'));
				}
				$this->redirect('DatabaseBackupList');
			}
			$date_heure = explode('_', $this->_getArg('fichier'));
			$heure = str_replace('-',':',$date_heure[1]);
			import('SauvegardeDeleteView');
			$v = new SauvegardeDeleteView();
			$v->show(array('fichier' => $this->_getArg('fichier'), 'date' => $date_heure[0], 'heure' => $heure));
		}
	}
	
	/* Fonction pour ajouter une sauvegarde */
	function _doAdd() {
		if ($_SESSION['user_privileges'] == 'administrateur') {
			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				if (isset($_POST['validation'])) {
					/* Ajout de la sauvegarde sur le serveur */
					exec ("mysqldump --databases schoollog > ". SAVESQL_PATH . date("Y-m-d") . "_" . date("H-i-s") . "_backupBDD.sql -u epsidev -pepsidev", $output, $error);
					/* S'il y a une erreur on l'affiche, sinon on affiche action réalisée avec succès */
					if(!empty($error)) {
						$_SESSION['ERROR_MSG'] = 'La sauvegarde de la base de données a échouée';
						unlink (SAVESQL_PATH . date("Y-m-d") . "_" . date("H-i-s") . "_backupBDD.sql");
					}
					else $_SESSION['INFO_MSG'] = 'La base de données a été sauvegardée avec succès';
				}
				$this->redirect('DatabaseBackupList');
			}
			import('SauvegardeAddView');
			$v = new SauvegardeAddView();
			$v->show(array());
		}
	}
	
	/* Fonction pour restaurer une sauvegarde */
	function _doRestore() {
		if ($_SESSION['user_privileges'] == 'administrateur') {
			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				if (isset($_POST['validation'])) {
					if (file_exists(SAVESQL_PATH . $_GET['fichier']))
					{
						/* Restauration de la sauvegarde */
						exec ("mysql -u epsidev -pepsidev < " . SAVESQL_PATH . $_GET['fichier'], $output, $error);
						/* S'il y a une erreur on l'affiche, sinon on affiche action réalisée avec succès */
						if(!empty($error)) {
							$_SESSION['ERROR_MSG'] = 'La restauration de la base de données a échouée';
						}
						else $_SESSION['INFO_MSG'] = 'La base de données a été restaurée avec succès';
					}
				}
				$this->redirect('DatabaseBackupList');
			}
			$fichier = $this->_getArg('fichier');
			$date_heure = explode('_',$fichier);
			$heure = str_replace('-',':',$date_heure[1]);
			import('SauvegardeRestoreView');
			$v = new SauvegardeRestoreView();
			$v->show(array('fichier' => $this->_getArg('fichier'), 'date' => $date_heure[0], 'heure' => $heure));		
		}
	}
}
?>
