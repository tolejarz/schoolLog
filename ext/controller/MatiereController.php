<?php
class MatiereController extends Controller {
    /* Fonction pour afficher la liste des matières */
    public function doList() {
        if ($_SESSION['user_privileges'] == 'superviseur') {
            /* Récupération de l'id et du nom de toutes les matières dans la base */
            $m = new MatiereModel();
            $v = new MatiereDefaultView();
            $v->show(array('subjects' => $m->listing()));
        }
    }
    
    /* Fonction pour supprimer une matière */
    public function doDelete($args) {
        $subject_id = $args['subject_id'];
        if ($_SESSION['user_privileges'] == 'superviseur') {
            $m = new MatiereModel();
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['validation'])) {
                    /*Suppression la matière dans la base */
                    $m->delete($subject_id);
                }
                Router::redirect('SubjectList');
            }
            /* Récupération des informations associées à la matière dans la base */
            $r = $m->get(array('id' => $subject_id));
            $v = new MatiereDeleteView();
            $v->show(array('id' => $subject_id, 'nom' => $r['nom']));
        }
    }
    
    /* Fonction pour ajouter une matière */
    public function doAdd() {
        if ($_SESSION['user_privileges'] == 'superviseur') {
            /* Récupération de l'id et du libelle de toutes les classes dans la base */
            $m = new ClasseModel();
            $classes = $m->listing();
            
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['annulation'])) {
                    Router::redirect('SubjectList');
                }
                
                /* Gestion des erreurs */
                if (empty($_POST['nom'])) {
                    $_SESSION['ERROR_MSG'] = 'Veuillez remplir le champ <b>Nom</b>';
                } else {
                    if($this->dbo->sqleval('select count(*) from matieres where nom="' . $_POST['nom'] . '"') > 0) {
                        $_SESSION['ERROR_MSG'] = 'Cette matière existe déjà';
                    }
                }
                /* Fin de la gestion des erreurs */
                
                if (!isset($_SESSION['ERROR_MSG'])) {
                    if (isset($_POST['validation'])) {
                        /* S'il n'y a pas d'erreurs, ajout de la matière dans la base */
                        $m = new MatiereModel();
                        $id_matiere = $m->create(array('nom' => $_POST['nom']));
                        /* Pour chaque classe cochée on crée une entrée qui l'associe à la matière dans la base */
                        $m = new MatieresClasseModel();
                        foreach($classes as $c)
                        {
                            if(isset($_POST['c_'.$c['id']])) {
                                $m->create(array('id_classe' => $c['id'], 'id_matiere' => $id_matiere));
                            }
                        }
                    }
                    Router::redirect('SubjectList');
                }
            }
            $v = new MatiereAddView();
            $v->show(array('classes' => $classes));
        }
    }
    
    /* Fonction pour éditer le nom d'une matière */
    public function doEdit($args) {
        $subject_id = $args['subject_id'];
        if ($_SESSION['user_privileges'] == 'superviseur') {
            /* Récupération des informations associées à la matière dans la base */
            $m = new MatiereModel();
            $r = $m->get(array('id' => $subject_id));
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['annulation'])) {
                    Router::redirect('SubjectList');
                }
                
                /* Gestion des erreurs */
                if (empty($_POST['nom'])) {
                    $_SESSION['ERROR_MSG'] = 'Veuillez remplir le champ <b>Nom</b>';
                } else {
                    if($this->dbo->sqleval('select count(*) from matieres where id!=' . $subject_id . ' and nom="' . $_POST['nom'] . '"') > 0) {
                        $_SESSION['ERROR_MSG'] = 'Cette matière existe déjà';
                    }
                }
                /* Fin de la gestion des erreurs */
                
                if (!isset($_SESSION['ERROR_MSG'])) {
                    if (isset($_POST['validation']) && $_POST['nom'] != $r['nom']) {
                        /* S'il n'y a pas d'erreurs, et que le nom est différent du précédent, mise à jour de la matière dans la base */
                        $m->update($subject_id, array('nom' => $_POST['nom']));
                    }
                    Router::redirect('SubjectList');
                }
            }
            $v = new MatiereEditView();
            $v->show(array('id' => $subject_id, 'nom' => $r['nom']));
        }
    }
}
?>
