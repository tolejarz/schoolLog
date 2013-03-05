<?php
class UserController extends Controller {
    /* Fonction pour afficher la liste des enseignants */
    public function doList() {
        if (in_array($_SESSION['user_privileges'], array('superviseur', 'administrateur'))) {
            /* r�cup�ration de la liste des enseignants dans la base de donn�es */
            $m = new UserModel($this->dbo);
            $v = new UserDefaultView();
            $v->show(array('enseignants' => $m->listingEnseignants()));
        }
    }
    
    /* Fonction pour supprimer un enseignant */
    public function doDelete($args) {
        $user_id = $args['user_id'];
        
        if (in_array($_SESSION['user_privileges'], array('superviseur', 'administrateur'))) {
            $m = new UserModel($this->dbo);
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['validation'])) {
                    /* Suppression de l'enseignant */
                    $m->delete($user_id);
                }
                Router::redirect('UserList');
            }
            /* R�cup�ration des informations de l'enseignant */
            $r = $m->get(array('id' => $user_id));
            $v = new UserDeleteView();
            $v->show($r);
        }
    }
    
    /* Fonction pour ajouter un enseignant */
    public function doAdd(){
        if (in_array($_SESSION['user_privileges'], array('superviseur', 'administrateur'))) {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (!isset($_POST['validation'])) {
                    Router::redirect('UserList');
                }
                
                $login = strtolower($_POST['nom']);
                if (empty($login)) {
                    $_SESSION['ERROR_MSG'] = 'Veuillez remplir le champ Nom';
                } else if ($this->dbo->sqleval('select count(*) from utilisateurs where login="' . $login . '"') > 0) {
                    $_SESSION['ERROR_MSG'] = 'Cet utilisateur existe d�j�';
                } else {
                    //$infosLDAP = recupererInfos(LDAP_SERVER, $login);
                    if (!empty($infosLDAP)) {
                        $_SESSION['ERROR_MSG'] = 'Cet utilisateur n\'existe pas dans le serveur LDAP';
                    } else {
                        $parms = array(
                            'droits'        => 'enseignant',
                            'login'         => $login,
                            'civility'      => $_POST['civility'],
                            'nom'           => $_POST['nom'],
                            'email'         => $infosLDAP['email']
                        );
                        
                        $m = new UserModel($this->dbo);
                        $id = $m->create($parms);
                        
                        $m = new MatieresClasseModel($this->dbo);
                        foreach ($_POST as $key => $value) {
                            if (substr($key, 0, 3) == 'cm_') {
                                $ids = explode('_', substr($key, 3));
                                $m->create(array('id_enseignant' => $id, 'id_classe' => $ids[0], 'id_matiere' => $ids[1]));
                            }
                        }
                    }
                    Router::redirect('UserList');
                }
            }
            
            /* r�cup�ration des classes / mati�res */
            $m = new ClasseModel($this->dbo);
            $resc = $m->listing();
            $m = new MatieresClasseModel($this->dbo);
            $classes = array();
            foreach ($resc as $c) {
                $matieres =  $m->getSubjectsClass(array('id' => $c['id']));
                $classes[] = array('id' => $c['id'], 'libelle' => $c['libelle'], 'matieres' => $matieres);
            }
            $v = new UserAddView();
            $v->show(array('classes' => $classes));
        }
    }
    
    /* Fonction pour �diter un enseignant */
    public function doEdit($args) {
        $user_id = $args['user_id'];
        
        if (in_array($_SESSION['user_privileges'], array('superviseur', 'administrateur'))) {
            $m = new UserModel($this->dbo);
            $r = $m->get(array('id' => $user_id));
            
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['annulation'])) {
                    Router::redirect('UserList');
                }
                /* Gestion des erreurs */
                if (empty($_POST['nom'])) {
                    $_SESSION['ERROR_MSG'] = 'Veuillez remplir le champ Nom';
                } elseif ($this->dbo->sqleval('select count(*) from utilisateurs where id!=' . $user_id . ' and login="' . $login . '"') > 0) {
                    $_SESSION['ERROR_MSG'] = 'Cet utilisateur existe d�j�';
                }
                /* Fin de la gestion des erreurs */
                
                if (!isset($_SESSION['ERROR_MSG'])) {
                    if (isset($_POST['validation']) && ($_POST['nom'] != $r['nom'] || $_POST['civility'] != $r['civility'])) {
                        $parms = array(
                            'nom'           => $_POST['nom'],
                            'civility'      => $_POST['civility'],
                        );
                        $m->update($user_id, $parms);
                    }
                    
                    $r = $this->dbo->delete('delete from enseignants_matieres_classes where id_enseignant=' . $user_id);
                    $m = new MatieresClasseModel($this->dbo);
                    foreach ($_POST as $key => $value) {
                        if (substr($key, 0, 3) == 'cm_') {
                            $ids = explode('_', substr($key, 3));
                            $m->create(array('id_enseignant' => $user_id, 'id_classe' => $ids[0], 'id_matiere' => $ids[1]));
                        }
                    }
                    Router::redirect('UserList');
                }
            }
            
            /* r�cup�ration des mati�res de chaque classe */
            $m = new ClasseModel($this->dbo);
            $resc = $m->listing();
            $m = new MatieresClasseModel($this->dbo);
            $classes = array();
            foreach ($resc as $c) {
                $matieres =  $m->getSubjectsClass(array('id' => $c['id']));
                $classes[] = array('id' => $c['id'], 'libelle' => $c['libelle'], 'matieres' => $matieres);
            }
            
            /* r�cup�ration des mati�res de l'enseignant s�lectionn� */
            $m = new MatieresClasseModel($this->dbo);
            $matieres_enseignant = $m->getSubjectsEnseignant(array('id' => $user_id));
            $params = array(
                'id'                    => $user_id,
                'login'                 => $r['login'],
                'nom'                   => $r['nom'],
                'civility'              => $r['civility'],
                'classes'               => $classes,
                'matieres_enseignant'   => $matieres_enseignant
            );
            $v = new UserEditView();
            $v->show($params);
        }
    }
}
?>
