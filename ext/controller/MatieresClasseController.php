<?php
// No SQL!!! :)
class MatieresClasseController extends Controller {
    /* Fonction pour afficher la liste des matières et des enseignants associés pour une classe */  
    public function doList() {
        if ($_SESSION['user']['privileges'] == 'superviseur') {
            /* Récupération de l'id et du libellé de toutes les classes dans la base */
            $class = new ClasseModel();
            $classes = $class->search();
            
            /* Si aucune classe n'est passée en paramètres, on affiche les informations de la première classe disponible dans la base */
            $id_classe = $this->_getArg('id_classe');
            if (empty($id_classe)) {
                $id_classe = current($classes)['id'];
            }
            
            $v = new MatieresClasseSelectView();
            $v->show(array('classes' => $classes, 'id_classe' => $id_classe));
            
            /* Récupération de l'id, du nom et de l'ensemble des enseignants (id, nom) des matières associées à la classe dans la base */
            $subjects = array();
            $m = new EnseignantsMatieresClassesModel();
            $resm = $m->search(array('id_classe' => $id_classe));
            foreach ($resm as $rm) {
                if (!isset($subjects[$rm['id_matiere']])) {
                    $subjects[$rm['id_matiere']] = $rm;
                }
                if (!isset($subjects[$rm['id_matiere']]['enseignants'][$rm['id_enseignant']])) {
                    $user = new UserModel();
                    $user->get($rm['id_enseignant']);
                    $subjects[$rm['id_matiere']]['enseignants'][$rm['id_enseignant']] = $user->toArray();
                }
            }
            
            /* Récupération du libellé de la classe dans la base */
            $class = new ClasseModel();
            $class->get($id_classe);
            
            $v = new MatieresClasseDefaultView();
            $v->show(array('subjects' => $subjects, 'id_classe' => $id_classe, 'classe' => $class->libelle));
        }
    }
    
    /* Fonction pour supprimer une matière d'une classe (supprime également tous ses enseignants associés) */   
    public function doDelete($args) {
        $class_id = $args['class_id'];
        if ($_SESSION['user']['privileges'] == 'superviseur') {
            $m = new EnseignantsMatieresClassesModel();
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['validation'])) {
                    /* Suppression de toutes les associations matière - classe dans la base */
                    $m->delete(array('id_classe' => $class_id, 'id_matiere' => $this->_getArg('id_matiere')));
                }
                Router::redirect('ClassSubjectList', array('class_id' => $class_id));
            }
            /* Récupération des informations associées à la matière de la classe dans la base */
            $m->get(array('id_classe' => $class_id, 'id_matiere' => $this->_getArg('id_matiere')));
            $params = array(
                'id_classe' => $class_id,
                'id_matiere' => $this->_getArg('id_matiere'),
                'matiere' => $m->nom_matiere,
                'classe' => $m->nom_classe
            );
            $v = new MatieresClasseDeleteView();
            $v->show($params);
        }
    }
    
    /* Fonction pour ajouter une matière à une classe (choix du ou des enseignants associés (optionnel)  */ 
    public function doAdd($args) {
        $class_id = $args['class_id'];
        if ($_SESSION['user']['privileges'] == 'superviseur') {
            /* Récupération de l'id et du nom de tous les enseignants dans la base */
            $user = new UserModel();
            $enseignants = $user->search(array('droits find' => 'enseignant'));
            
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['validation'])) {
                    $nb_add = 0;
                    /* Pour chaque enseignant coché on crée une entrée qui l'associe à la matière de la classe dans la base */
                    foreach ($enseignants as $e) {
                        if (isset($_POST['e_'.$e['id']])) {
                            $m = new EnseignantsMatieresClassesModel(array(
                                'id_enseignant' => $e['id'],
                                'id_classe'     => $class_id,
                                'id_matiere'    => $_POST['id_matiere']
                            ));
                            $m->save();
                            $nb_add++;
                        }
                    }
                    /* Si on a ajouté aucun enseignant dans la matière de la classe, on ajoute la matière à la classe sans enseignant dans la base */
                    if (empty($nb_add)) {
                        $m = new EnseignantsMatieresClassesModel(array(
                            'id_classe'     => $class_id,
                            'id_matiere'    => $_POST['id_matiere']
                        ));
                        $m->save();
                    }
                }
                Router::redirect('ClassSubjectList', array('class_id' => $class_id));
            }
            
            /* Récupération de l'id et du nom de toutes les matières qui ne sont pas associées à la classe dans la base */
            $m = new EnseignantsMatieresClassesModel();
            $m = $m->search(array('id_classe' => $class_id), array('indexedby' => 'id_matiere'));
            
            $subject = new MatiereModel();
            $subjects = $subject->search();
            
            foreach ($subjects as $i => $subject) {
                if (array_key_exists($subject['id'], $m)) {
                    unset($subjects[$i]);
                }
            }
            
            /* Récupération du libellé de la classe dans la base */
            $class = new ClasseModel();
            $class->get($class_id);
            
            $params = array(
                'subjects'      => $subjects,
                'enseignants'   => $enseignants,
                'id_classe'     => $class_id,
                'classe'        => $class->libelle
            );
            $v = new MatieresClasseAddView();
            $v->show($params);
        }
    }
    
    /* Fonction pour éditer les enseignants associés à une matière */
    public function doEdit($args) {
        $class_id = $args['class_id'];
        if ($_SESSION['user']['privileges'] == 'superviseur') {
            /* Récupération des informations associées à la matière de la classe dans la base */
            $m = new EnseignantsMatieresClassesModel();
            
            /* Récupération de l'id et du nom de tous les enseignants dans la base */
            $user = new UserModel();
            $enseignants = $user->search(array('droits find' => 'enseignant'));
            
            /* Récupération de tous les id des enseignants associés à la matière de la classe dans la base */
            $enseignants_matiere = $m->search(array('id_classe' => $class_id, 'id_matiere' => $this->_getArg('id_matiere')));
            
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['validation'])) {
                    $m = new EnseignantsMatieresClassesModel();
                    $m->delete(array('id_classe' => $class_id, 'id_matiere' => $this->_getArg('id_matiere')));
                    
                    $hasEnseignants = false;
                    foreach ($_POST as $key => $value) {
                        if (substr($key, 0, 2) == 'e_') {
                            $hasEnseignants = true;
                            $m = new EnseignantsMatieresClassesModel(array('id_enseignant' => substr($key, 2), 'id_classe' => $class_id, 'id_matiere' => $this->_getArg('id_matiere')));
                            $m->save();
                        }
                    }
                    if (!$hasEnseignants) {
                        $m = new EnseignantsMatieresClassesModel(array('id_classe' => $class_id, 'id_matiere' => $this->_getArg('id_matiere')));
                        $m->save();
                    }
                }
                Router::redirect('ClassSubjectList', array('class_id' => $class_id));
            }
            $params = array(
                'enseignants'               => $enseignants,
                'enseignants_matiere'       => $enseignants_matiere,
                'id_classe'                 => $class_id,
                'id_matiere'                => $this->_getArg('id_matiere'),
                'classe'                    => $m->nom_classe,
                'matiere'                   => $m->nom_matiere
            );
            $v = new MatieresClasseEditView();
            $v->show($params);
        }
    }
}
?>
