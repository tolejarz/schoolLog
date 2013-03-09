<?php
class ClasseController extends Controller {
    /* Fonction pour afficher la liste des classes */
    public function doList() {
        if ($_SESSION['user_privileges'] == 'superviseur') {
            /* Récupération de l'id, du libellé et de l'email de toutes les classes dans la base */
            $m = new ClasseModel();
            $v = new ClasseDefaultView();
            $v->show(array('classes' => $m->listing()));
        }
    }
    
    /* Fonction pour supprimer une classe */
    public function doDelete($args) {
        $class_id = $args['class_id'];
        if ($_SESSION['user_privileges'] == 'superviseur') {
            $m = new ClasseModel();
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['validation'])) {
                    /*Suppression de la classe dans la base */
                    $m->delete($class_id);
                }
                Router::redirect('ClassList');
            }
            /* Récupération des informations associées à la classe dans la base */
            $r = $m->get(array('id' => $class_id));
            $v = new ClasseDeleteView();
            $v->show(array('id' => $class_id, 'libelle' => $r['libelle']));
        }
    }
    
    /* Fonction pour ajouter une classe */
    public function doAdd() {
        if ($_SESSION['user_privileges'] == 'superviseur') {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['annulation'])) {
                    Router::redirect('ClassList');
                }
                
                /* Gestion des erreurs */
                if (empty($_POST['libelle'])) {
                    $_SESSION['ERROR_MSG'] = 'Veuillez remplir le champ <b>Libellé</b>';
                } else if (empty($_POST['email'])) {
                    $_SESSION['ERROR_MSG'] = 'Veuillez remplir le champ <b>Email</b>';
                } else if (!eregi('^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$', $_POST['email'])) {
                    $_SESSION['ERROR_MSG'] = 'Email incorrect. Veuillez entrer un email de la forme <b>xx@yy.zz</b>';
                } else {
                    if ($this->dbo->sqleval('select count(*) from classes where libelle="' . $_POST['libelle'] . '"') > 0) {
                        $_SESSION['ERROR_MSG'] = 'Cette classe existe déjà';
                    }
                }
                /* Fin de la gestion des erreurs */
                
                if (!isset($_SESSION['ERROR_MSG'])) {
                    if (isset($_POST['validation'])) {
                        /* S'il n'y a pas d'erreurs, ajout de la classe dans la base */
                        $s = new ClasseModel();
                        $s->create(array('libelle' => $_POST['libelle'], 'email' => $_POST['email']));
                    }
                    Router::redirect('ClassList');
                }
            }
            $v = new ClasseAddView();
            $v->show(array());
        }
    }
    
    /* Fonction pour éditer le libellé et l'adresse email d'une classe */
    public function doEdit($args) {
        $class_id = $args['class_id'];
        if ($_SESSION['user_privileges'] == 'superviseur') {
            /* Récupération des informations associées à la classe dans la base */
            $m = new ClasseModel();
            $r = $m->get(array('id' => $class_id));
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['annulation'])) {
                    Router::redirect('ClassList');
                }
                
                /* Gestion des erreurs */
                if (empty($_POST['libelle'])) {
                    $_SESSION['ERROR_MSG'] = 'Veuillez remplir le champ <b>Libellé</b>';
                } else if (empty($_POST['email'])) {
                    $_SESSION['ERROR_MSG'] = 'Veuillez remplir le champ <b>Email</b>';
                } else if (!eregi('^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$', $_POST['email'])) {
                    $_SESSION['ERROR_MSG'] = 'Email incorrect. Veuillez entrer un email de la forme <b>xx@yy.zz</b>';
                } else {
                    if ($this->dbo->sqleval('select count(*) from classes where id!=' . $class_id . ' and libelle="' . $_POST['libelle'] . '"') > 0) {
                        $_SESSION['ERROR_MSG'] = 'Cette classe existe déjà';
                    }
                }
                /* Fin de la gestion des erreurs */
                
                if (!isset($_SESSION['ERROR_MSG'])) {
                    if (isset($_POST['validation']) && ($_POST['libelle'] != $r['libelle'] || $_POST['email'] != $r['email'])) {
                        /* S'il n'y a pas d'erreurs, et que le libellé de classe ou l'adresse email est différent du précédent, mise à jour de la classe dans la base */
                        $m->update($class_id, array('libelle' => $_POST['libelle'], 'email' => $_POST['email']));
                    }
                    Router::redirect('ClassList');
                }
            }
            $params = array('id' => $class_id, 'libelle' => $r['libelle'], 'email' => $r['email']);
            $v = new ClasseEditView();
            $v->show($params);
        }
    }
}
?>
