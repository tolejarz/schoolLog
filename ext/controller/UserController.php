<?php
// No SQL!!! :)
class UserController extends Controller {
    public function doAdd(){
        if (in_array($_SESSION['user']['privileges'], array('superviseur', 'administrateur'))) {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (!isset($_POST['validation'])) {
                    Router::redirect('UserList');
                }
                
                $login = strtolower($_POST['nom']);
                //$infosLDAP = recupererInfos(LDAP_SERVER, $login);
                /*if (!empty($infosLDAP)) {
                    $_SESSION['ERROR_MSG'] = 'Cet utilisateur n\'existe pas dans le serveur LDAP';
                } else {
                */
                $user = new UserModel(array(
                    'droits'        => 'enseignant',
                    'login'         => $login,
                    'civility'      => $_POST['civility'],
                    'nom'           => $_POST['nom'],
                    'email'         => $infosLDAP['email']
                ));
                $user->save();
                
                foreach ($_POST['emc'] as $class_id => $id_matieres) {
                    foreach ($id_matieres as $id_matiere) {
                        $m = new EnseignantsMatieresClassesModel(array(
                            'id_enseignant' => $user->id,
                            'id_classe'     => $class_id,
                            'id_matiere'    => $id_matiere,
                        ));
                        $m->save();
                    }
                }
                //}
                Router::redirect('UserList');
            }
            
            $class = new ClasseModel();
            $classes = $class->search();
            
            $m = new EnseignantsMatieresClassesModel();
            foreach ($classes as $i => $c) {
                $classes[$i]['matieres'] = $m->search(array('id_classe' => $c['id']));
            }
            
            $v = new UserAddView();
            $v->show(array('classes' => $classes));
        }
    }
    
    public function doDelete($args) {
        $user_id = $args['user_id'];
        if (in_array($_SESSION['user']['privileges'], array('superviseur', 'administrateur'))) {
            $user = new UserModel();
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['validation'])) {
                    $user->delete(array('id' => $user_id));
                }
                Router::redirect('UserList');
            }
            $user->get($user_id);
            
            $v = new UserDeleteView();
            $v->show($user->toArray());
        }
    }
    
    public function doEdit($args) {
        $user_id = $args['user_id'];
        if (in_array($_SESSION['user']['privileges'], array('superviseur', 'administrateur'))) {
            $user = new UserModel();
            $user->get($user_id);
            
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['validation'])) {
                    $user->nom = $_POST['nom'];
                    $user->civility = $_POST['civility'];
                    $user->save();
                    
                    $m = new EnseignantsMatieresClassesModel();
                    $m->delete(array('id_enseignant' => $user_id));
                    
                    foreach ($_POST['emc'] as $class_id => $id_matieres) {
                        foreach ($id_matieres as $id_matiere) {
                            $m = new EnseignantsMatieresClassesModel(array(
                                'id_enseignant' => $user_id,
                                'id_classe'     => $class_id,
                                'id_matiere'    => $id_matiere,
                            ));
                            $m->save();
                        }
                    }
                }
                Router::redirect('UserList');
            }
            
            $class = new ClasseModel();
            $classes = $class->search();
            
            $m = new EnseignantsMatieresClassesModel();
            foreach ($classes as $i => $c) {
                $classes[$i]['matieres'] = $m->search(array('id_classe' => $c['id']));
            }
            
            $m = new EnseignantsMatieresClassesModel();
            $params = array(
                'id'                    => $user_id,
                'login'                 => $user->login,
                'nom'                   => $user->nom,
                'civility'              => $user->civility,
                'classes'               => $classes,
                'matieres_enseignant'   => $m->search(array('id_enseignant' => $user_id))
            );
            $v = new UserEditView();
            $v->show($params);
        }
    }
    
    public function doList() {
        if (in_array($_SESSION['user']['privileges'], array('superviseur', 'administrateur'))) {
            $user = new UserModel();
            
            $v = new UserDefaultView();
            $v->show(array('enseignants' => $user->search(array('droits find' => 'enseignant'))));
        }
    }
}
?>
