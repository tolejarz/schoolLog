<?php
// No SQL!!! :)
class EquipmentController extends Controller {
    public function doAdd() {
        if ($_SESSION['user']['privileges'] == 'superviseur') {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['validation'])) {
                    $materiel = new MaterielModel(array(
                        'type'      => $_POST['type'],
                        'modele'    => $_POST['modele'],
                    ));
                    $materiel->save();
                }
                Router::redirect('EquipmentList');
            }
            $v = new MaterielAddView();
            $v->show();
        }
    }
    
    public function doDelete($args) {
        $equipment_id = $args['equipment_id'];
        if ($_SESSION['user']['privileges'] == 'superviseur') {
            $equipment = new MaterielModel();
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['validation'])) {
                    $equipment->delete(array('id' => $equipment_id));
                }
                Router::redirect('EquipmentList');
            }
            $v = new MaterielDeleteView();
            $equipment->get($equipment_id);
            $v->show($equipment->toArray());
        }
    }
    
    public function doEdit($args) {
        $equipment_id = $args['equipment_id'];
        if ($_SESSION['user']['privileges'] == 'superviseur') {
            $equipment = new MaterielModel();
            $equipment->get($equipment_id);
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['validation'])) {
                    $equipment->type    = $_POST['type'];
                    $equipment->modele  = $_POST['modele'];
                    $equipment->etat    = $_POST['etat'];
                    $equipment->save();
                }
                Router::redirect('EquipmentList');
            }
            $v = new MaterielEditView();
            $v->show($equipment->toArray());
        }
    }
    
    public function doList() {
        if (in_array($_SESSION['user']['privileges'], array('superviseur', 'enseignant'))) {
            $v = new MaterielDefaultView();
            $equipment = new MaterielModel();
            $v->show(array('equipments' => $equipment->search()));
        }
    }
}
?>
