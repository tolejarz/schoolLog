<?php
// No SQL!!! :)
class MatieresClasseController extends Controller {
    /* Fonction pour afficher la liste des mati�res et des enseignants associ�s pour une classe */  
    public function doList() {
        if ($_SESSION['user']['privileges'] == 'superviseur') {
            /* R�cup�ration de l'id et du libell� de toutes les classes dans la base */
            $class = new ClasseModel();
            $classes = $class->search();
            
            /* Si aucune classe n'est pass�e en param�tres, on affiche les informations de la premi�re classe disponible dans la base */
            $id_classe = $this->_getArg('id_classe');
            if (empty($id_classe)) {
                $id_classe = current($classes)['id'];
            }
            
            $v = new MatieresClasseSelectView();
            $v->show(array('classes' => $classes, 'id_classe' => $id_classe));
            
            /* R�cup�ration de l'id, du nom et de l'ensemble des enseignants (id, nom) des mati�res associ�es � la classe dans la base */
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
            
            /* R�cup�ration du libell� de la classe dans la base */
            $class = new ClasseModel();
            $class->get($id_classe);
            
            $v = new MatieresClasseDefaultView();
            $v->show(array('subjects' => $subjects, 'id_classe' => $id_classe, 'classe' => $class->libelle));
        }
    }
    
    /* Fonction pour supprimer une mati�re d'une classe (supprime �galement tous ses enseignants associ�s) */   
    public function doDelete($args) {
        $class_id = $args['class_id'];
        if ($_SESSION['user']['privileges'] == 'superviseur') {
            $m = new EnseignantsMatieresClassesModel();
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['validation'])) {
                    /* Suppression de toutes les associations mati�re - classe dans la base */
                    $m->delete(array('id_classe' => $class_id, 'id_matiere' => $this->_getArg('id_matiere')));
                }
                Router::redirect('ClassSubjectList', array('class_id' => $class_id));
            }
            /* R�cup�ration des informations associ�es � la mati�re de la classe dans la base */
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
    
    /* Fonction pour ajouter une mati�re � une classe (choix du ou des enseignants associ�s (optionnel)  */ 
    public function doAdd($args) {
        $class_id = $args['class_id'];
        if ($_SESSION['user']['privileges'] == 'superviseur') {
            /* R�cup�ration de l'id et du nom de tous les enseignants dans la base */
            $user = new UserModel();
            $enseignants = $user->search(array('droits find' => 'enseignant'));
            
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['validation'])) {
                    $nb_add = 0;
                    /* Pour chaque enseignant coch� on cr�e une entr�e qui l'associe � la mati�re de la classe dans la base */
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
                    /* Si on a ajout� aucun enseignant dans la mati�re de la classe, on ajoute la mati�re � la classe sans enseignant dans la base */
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
            
            /* R�cup�ration de l'id et du nom de toutes les mati�res qui ne sont pas associ�es � la classe dans la base */
            $m = new EnseignantsMatieresClassesModel();
            $m = $m->search(array('id_classe' => $class_id), array('indexedby' => 'id_matiere'));
            
            $subject = new MatiereModel();
            $subjects = $subject->search();
            
            foreach ($subjects as $i => $subject) {
                if (array_key_exists($subject['id'], $m)) {
                    unset($subjects[$i]);
                }
            }
            
            /* R�cup�ration du libell� de la classe dans la base */
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
    
    /* Fonction pour �diter les enseignants associ�s � une mati�re */
    public function doEdit($args) {
        $class_id = $args['class_id'];
        if ($_SESSION['user']['privileges'] == 'superviseur') {
            /* R�cup�ration des informations associ�es � la mati�re de la classe dans la base */
            $m = new EnseignantsMatieresClassesModel();
            
            /* R�cup�ration de l'id et du nom de tous les enseignants dans la base */
            $user = new UserModel();
            $enseignants = $user->search(array('droits find' => 'enseignant'));
            
            /* R�cup�ration de tous les id des enseignants associ�s � la mati�re de la classe dans la base */
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
