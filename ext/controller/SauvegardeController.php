<?php
class SauvegardeController extends Controller {
    
    /* Fonction pour afficher la liste du matériel */
    public function doList() {
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
            
            $v = new SauvegardeDefaultView();
            $v->show(array('sauvegardes' => $sauvegardes));
        }
    }
    
    /* Fonction pour supprimer une sauvegarde */
    public function doDelete($args) {
        $filename = $args['filename'];
        if ($_SESSION['user_privileges'] == 'administrateur') {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['validation'])) {
                    /*Suppression de la sauvegarde sur le serveur */
                    unlink (SAVESQL_PATH . $filename);
                }
                Router::redirect('BackupList');
            }
            $date_heure = explode('_', $filename);
            $heure = str_replace('-',':',$date_heure[1]);
            $v = new SauvegardeDeleteView();
            $v->show(array('fichier' => $filename, 'date' => $date_heure[0], 'heure' => $heure));
        }
    }
    
    /* Fonction pour ajouter une sauvegarde */
    public function doAdd() {
        if ($_SESSION['user_privileges'] == 'administrateur') {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['validation'])) {
                    /* Ajout de la sauvegarde sur le serveur */
                    exec ("mysqldump --databases schoollog > ". SAVESQL_PATH . date("Y-m-d") . "_" . date("H-i-s") . "_backupBDD.sql -u root -proot", $output, $error);
                    /* S'il y a une erreur on l'affiche, sinon on affiche action réalisée avec succès */
                    if(!empty($error)) {
                        $_SESSION['ERROR_MSG'] = 'La sauvegarde de la base de données a échouée';
                        unlink (SAVESQL_PATH . date("Y-m-d") . "_" . date("H-i-s") . "_backupBDD.sql");
                    }
                    else $_SESSION['INFO_MSG'] = 'La base de données a été sauvegardée avec succès';
                }
                Router::redirect('BackupList');
            }
            $v = new SauvegardeAddView();
            $v->show(array());
        }
    }
    
    /* Fonction pour restaurer une sauvegarde */
    public function doRestore($args) {
        $filename = $args['filename'];
        if ($_SESSION['user_privileges'] == 'administrateur') {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['validation'])) {
                    if (file_exists(SAVESQL_PATH . $_GET['fichier'])) {
                        /* Restauration de la sauvegarde */
                        exec ("mysql -u root -proot < " . SAVESQL_PATH . $filename, $output, $error);
                        /* S'il y a une erreur on l'affiche, sinon on affiche action réalisée avec succès */
                        if (!empty($error)) {
                            $_SESSION['ERROR_MSG'] = 'La restauration de la base de données a échouée';
                        } else {
                            $_SESSION['INFO_MSG'] = 'La base de données a été restaurée avec succès';
                        }
                    }
                }
                Router::redirect('BackupList');
            }
            $date_heure = explode('_', $filename);
            $heure = str_replace('-',':',$date_heure[1]);
            $v = new SauvegardeRestoreView();
            $v->show(array('fichier' => $filename, 'date' => $date_heure[0], 'heure' => $heure));
        }
    }
}
?>
