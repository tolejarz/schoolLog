<?php
// No SQL!!! :)
class SauvegardeController extends Controller {
    public function doList() {
        if ($_SESSION['user']['privileges'] == 'administrateur') {
            foreach (glob($this->configuration['path']['save_sql'] . '/*.sql') as $filename) {
                $filename = basename($filename);
                $date_heure = explode('_', $filename);
                $heure = str_replace('-',':',$date_heure[1]);
                $sauvegardes[] = array('fichier' => $filename, 'date' => $date_heure[0], 'heure' => $heure);
            }
            $v = new SauvegardeDefaultView();
            $v->show(array('sauvegardes' => $sauvegardes));
        }
    }
    
    /* Fonction pour supprimer une sauvegarde */
    public function doDelete($args) {
        $filename = $args['filename'];
        if ($_SESSION['user']['privileges'] == 'administrateur') {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['validation'])) {
                    unlink($this->configuration['path']['save_sql'] . $filename);
                }
                Router::redirect('BackupList');
            }
            $date_heure = explode('_', $filename);
            $heure = str_replace('-', ':', $date_heure[1]);
            $v = new SauvegardeDeleteView();
            $v->show(array('fichier' => $filename, 'date' => $date_heure[0], 'heure' => $heure));
        }
    }
    
    /* Fonction pour ajouter une sauvegarde */
    public function doAdd() {
        if ($_SESSION['user']['privileges'] == 'administrateur') {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['validation'])) {
                    /* Ajout de la sauvegarde sur le serveur */
                    exec("mysqldump --databases schoollog > ". $this->configuration['path']['save_sql'] . date("Y-m-d") . "_" . date("H-i-s") . "_backupBDD.sql -u root -proot", $output, $error);
                    /* S'il y a une erreur on l'affiche, sinon on affiche action réalisée avec succès */
                    if(!empty($error)) {
                        $_SESSION['ERROR_MSG'] = 'La sauvegarde de la base de données a échouée';
                        unlink ($this->configuration['path']['save_sql'] . date("Y-m-d") . "_" . date("H-i-s") . "_backupBDD.sql");
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
        if ($_SESSION['user']['privileges'] == 'administrateur') {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['validation'])) {
                    if (file_exists($this->configuration['path']['save_sql'] . $_GET['fichier'])) {
                        /* Restauration de la sauvegarde */
                        exec ("mysql -u root -proot < " . $this->configuration['path']['save_sql'] . $filename, $output, $error);
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
