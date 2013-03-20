<?php
// No SQL!!! :)
class ClassController extends Controller {
    public function doAdd() {
        if ($_SESSION['user']['privileges'] == 'superviseur') {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['validation'])) {
                    $class = new ClasseModel(array(
                        'libelle'   => $_POST['libelle'],
                        'email'     => $_POST['email']
                    ));
                    $class->save();
                }
                Router::redirect('ClassList');
            }
            $v = new ClasseAddView();
            $v->show();
        }
    }
    
    public function doDelete($args) {
        $class_id = $args['class_id'];
        if ($_SESSION['user']['privileges'] == 'superviseur') {
            $class = new ClasseModel();
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['validation'])) {
                    $class->delete(array('id' => $class_id));
                }
                Router::redirect('ClassList');
            }
            $v = new ClasseDeleteView();
            $class->get($class_id);
            $v->show($class->toArray());
        }
    }
    
    public function doEdit($args) {
        $class_id = $args['class_id'];
        if ($_SESSION['user']['privileges'] == 'superviseur') {
            $class = new ClasseModel();
            $class->get($class_id);
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['validation'])) {
                    $class->libelle = $_POST['libelle'];
                    $class->email = $_POST['email'];
                    $class->save();
                }
                Router::redirect('ClassList');
            }
            $v = new ClasseEditView();
            $v->show($class->toArray());
        }
    }
    
    public function doList() {
        if ($_SESSION['user']['privileges'] == 'superviseur') {
            $v = new ClasseDefaultView();
            $class = new ClasseModel();
            $v->show(array('classes' => $class->search()));
        }
    }
}
?>
