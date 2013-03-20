<?php
// No SQL!!! :)
class MatiereController extends Controller {
    public function doAdd() {
        if ($_SESSION['user']['privileges'] == 'superviseur') {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['validation'])) {
                    $matiere = new MatiereModel(array(
                        'nom' => $_POST['nom']
                    ));
                    $matiere->save();
                    
                    foreach ($_POST['class'] as $class_id) {
                        $m = new EnseignantsMatieresClassesModel(array(
                            'id_classe'     => $class_id,
                            'id_matiere'    => $matiere->id,
                        ));
                        $m->save();
                    }
                }
                Router::redirect('SubjectList');
            }
            $v = new MatiereAddView();
            $class = new ClasseModel();
            $classes = $class->search();
            $v->show(array('classes' => $classes));
        }
    }
    
    public function doDelete($args) {
        $subject_id = $args['subject_id'];
        if ($_SESSION['user']['privileges'] == 'superviseur') {
            $matiere = new MatiereModel();
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['validation'])) {
                    $matiere->delete(array('id' => $subject_id));
                }
                Router::redirect('SubjectList');
            }
            $matiere->get($subject_id);
            $v = new MatiereDeleteView();
            $v->show(array('id' => $subject_id, 'nom' => $matiere->nom));
        }
    }
    
    public function doEdit($args) {
        $subject_id = $args['subject_id'];
        if ($_SESSION['user']['privileges'] == 'superviseur') {
            $matiere = new MatiereModel();
            $matiere->get($subject_id);
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['validation'])) {
                    $matiere->nom = $_POST['nom'];
                    $matiere->save();
                }
                Router::redirect('SubjectList');
            }
            $v = new MatiereEditView();
            $v->show($matiere->toArray());
        }
    }
    
    public function doList() {
        if ($_SESSION['user']['privileges'] == 'superviseur') {
            $v = new MatiereDefaultView();
            $matiere = new MatiereModel();
            $v->show(array('subjects' => $matiere->search()));
        }
    }
}
?>
