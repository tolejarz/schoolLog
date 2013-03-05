<?php
class MaterielController extends Controller {
    /* Fonction pour afficher la liste du matériel */
    public function doList() {
        if ($_SESSION['user_privileges'] == 'superviseur' || $_SESSION['user_privileges'] == 'enseignant') {
            /* Récupération de l'id, du type et du modèle de tous les matériels dans la base */
            $m = new MaterielModel($this->dbo);
            $v = new MaterielDefaultView();
            $v->show(array('equipments' => $m->listing()));
        }
    }
    
    /* Fonction pour supprimer un matériel */
    public function doDelete($args) {
        $equipment_id = $args['equipment_id'];
        if ($_SESSION['user_privileges'] == 'superviseur') {
            $m = new MaterielModel($this->dbo);
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['validation'])) {
                    /*Suppression du matériel dans la base */
                    $m->delete($equipment_id);
                }
                Router::redirect('EquipmentList');
            }
            /* Récupération des informations associées au matériel dans la base */
            $r = $m->get(array('id' => $equipment_id));
            $v = new MaterielDeleteView();
            $v->show(array('id' => $equipment_id, 'type' => $r['type'], 'modele' => $r['modele']));
        }
    }
    
    /* Fonction pour ajouter un matériel */
    public function doAdd() {
        if ($_SESSION['user_privileges'] == 'superviseur') {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['annulation'])) {
                    Router::redirect('EquipmentList');
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
                        $m = new MaterielModel($this->dbo);
                        $m->create(array('type' => $_POST['type'], 'modele' => $_POST['modele']));
                    }
                    Router::redirect('EquipmentList');
                }
            }
            $v = new MaterielAddView();
            $v->show(array());
        }
    }
    
    /* Fonction pour éditer le type et le modèle d'un matériel */
    public function doEdit($args) {
        $equipment_id = $args['equipment_id'];
        if ($_SESSION['user_privileges'] == 'superviseur') {
            /* Récupération des informations associées au matériel dans la base */
            $m = new MaterielModel($this->dbo);
            $r = $m->get(array('id' => $equipment_id));
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['annulation'])) {
                    Router::redirect('EquipmentList');
                }
                
                /* Gestion des erreurs */
                if (empty($_POST['type'])) {
                    $_SESSION['ERROR_MSG'] = 'Veuillez remplir le champ <b>Type de matériel</b>';
                } else if (empty($_POST['modele'])) {
                    $_SESSION['ERROR_MSG'] = 'Veuillez remplir le champ <b>Modèle</b>';
                } else {
                    if(($this->dbo->sqleval('select count(*) from materiels where id!=' . $equipment_id . ' and type="' . $_POST['type'] . '" and modele="' . $_POST['modele'] . '"')) > 0) {
                        $_SESSION['ERROR_MSG'] = 'Ce matériel existe déjà';
                    }
                }
                /* Fin de la gestion des erreurs */
                
                if (!isset($_SESSION['ERROR_MSG'])) {
                    if (isset($_POST['validation']) && ($_POST['type'] != $r['type'] || $_POST['modele'] != $r['modele']|| $_POST['etat'] != $r['etat'])) {
                        /* S'il n'y a pas d'erreurs, et que le type de matériel ou le modèle est différent du précédent, mise à jour du matériel dans la base */
                        $m->update($equipment_id, array('type' => $_POST['type'], 'modele' => $_POST['modele'], 'etat' => $_POST['etat']));
                    }
                    Router::redirect('EquipmentList');
                }
            }
            $params = array('id' => $equipment_id, 'type' => $r['type'], 'modele' => $r['modele'], 'etat' => $r['etat']);
            $v = new MaterielEditView();
            $v->show($params);
        }
    }
}
?>
