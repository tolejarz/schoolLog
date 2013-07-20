<?php
// No SQL!!! :)
class SupportController extends Controller {
    public function doList() {
        if ($_SESSION['user']['privileges'] == 'enseignant') {
            // Recherche des différentes classes de l'enseignant
            $support = new SupportModel();
            $supports = $support->search(array('id_enseignant' => $_SESSION['user']['id']));
            $_supports = array();
            
            foreach ($supports as $support) {
                if (!isset($_supports['classes'][$support['id_classe']])) {
                    $_supports['classes'][$support['id_classe']] = array(
                        'id_classe'     => $support['id_classe'],
                        'nom_classe'    => $support['nom_classe']
                    );
                }
                if (!isset($_supports['classes'][$support['id_classe']]['subjects'][$support['id_matiere']])) {
                    $_supports['classes'][$support['id_classe']]['subjects'][$support['id_matiere']] = array(
                        'id_matiere'    => $support['id_matiere'],
                        'nom_matiere'   => $support['nom_matiere']
                    );
                }
                if (!isset($_supports['classes'][$support['id_classe']]['subjects'][$support['id_matiere']]['supports'][$support['id']])) {
                    $_supports['classes'][$support['id_classe']]['subjects'][$support['id_matiere']]['supports'][$support['id']] = $support;
                }
            }
            $v = new SupportDefaultView();
            $v->show(array('supports' => $_supports, 'upload_path' => $this->configuration['path']['upload']));
        } elseif ($_SESSION['user']['privileges'] == 'eleve') {
            // Récupération de la liste des matières de la classe de l'élève
            $m = new EnseignantsMatieresClassesModel();
            $matieres = $m->search(array('id_classe' => $_SESSION['user']['class']));
            
            $id_matiere = $this->_getArg('id_matiere');
            $titre_on = $this->_getArg('titre_on');
            $mots_cles_on = $this->_getArg('mots_cles_on');
            $and = $this->_getArg('and');
            $mots_cles = $this->_getArg('mots_cles');
            // Préparation des différentes données utiles à l'affichage de la zone de recherche, puis affichage
            $parms = array(
                'mots_cles'     => $mots_cles,
                'and'           => $and,
                'matieres'      => $matieres,
                'id_matiere'    => $id_matiere,
                'titre_on'      => !empty($titre_on) || !isset($_POST['validation']),
                'mots_cles_on'  => !empty($mots_cles_on) || !isset($_POST['validation'])
            );
            
            $v = new SupportSearchView();
            $v->show($parms);
            
            if (!empty($id_matiere)) {
                // Ajout du filtre sur la matière si elle existe
                $whereMatiere = $id_matiere == -1 ? '' : ' and m.id=' . $id_matiere;
                
                // Filtres de recherche
                $mots = explode(' ', $this->_getArg('mots_cles'));
                
                $whereTitre = $whereTags = '';
                if (!empty($parms['titre_on'])) {
                    $tags = array();
                    foreach ($mots as $i => $mot) {
                        $tags[] = "s.titre like '%" . $mot . "%'";
                    }
                    $whereTitre = implode(!empty($and) ? ' and ' : ' or ', $tags);
                }
                if (!empty($parms['mots_cles_on'])) {
                    $tags = array();
                    foreach ($mots as $i => $mot) {
                        $tags[] = "s.tags like '%" . $mot . "%'";
                    }
                    $whereTags = implode(!empty($and) ? ' and ' : ' or ', $tags);
                }
                $whereTitreTags = $whereTitre . (!empty($whereTitre) && !empty($whereTags) ? ' or ' : '') . $whereTags;
                $where = !empty($whereTitreTags) ? ' and (' . $whereTitreTags . ')' : '';
                
                // Récupération des supports
                $support = new SupportModel();
                foreach ($matieres as &$matiere) {
                    $args['id_classe'] = $_SESSION['user']['class'];
                    $args['id_matiere'] = $matiere['id_matiere'];
                    $supports = $support->search($args);
                    $matiere['supports'] = $supports;
                }
                
                $classe = array('subjects' => $matieres);
                $classes = array($classe);
                $v = new SupportDefaultView();
                $v->show(array('supports' => array('classes' => $classes), 'upload_path' => $this->configuration['path']['upload']));
            }
        } elseif ($_SESSION['user']['privileges'] == 'superviseur') {
            // Récupération des différentes classes/matières auxquelles l'élève a accès afin de lui en offrir la liste
            $m = new EnseignantsMatieresClassesModel();
            $mc = $m->search();
            $classes = array();
            foreach ($mc as $c) {
                if (!isset($classes[$c['id_classe']])) {
                    $classes[$c['id_classe']] = array(
                        'id'        => $c['id_classe'],
                        'libelle'   => $c['nom_classe'],
                    );
                }
                if (!isset($classes[$c['id_classe']]['matieres'][$c['id_matiere']])) {
                    $classes[$c['id_classe']]['matieres'][$c['id_matiere']] = array(
                        'id'        => $c['id_matiere'],
                        'nom'       => $c['nom_matiere'],
                    );
                }
            }
            if (isset($_POST['id_matiere'])) {
                $ids = explode(';', $_POST['id_matiere']);
                $id_classe = $ids[0];
                $id_matiere = $id_classe == -1 ? -1 : $ids[1];
            } else {
                $id_classe = $this->_getArg('id_classe');
                $id_matiere = $this->_getArg('id_matiere');
            }
            $parms = array(
                'id_classe'     => $id_classe,
                'id_matiere'    => $id_matiere,
                'classes'       => $classes
            );
            
            $v = new SupportSearchView();
            $v->show($parms);
            
            if (!empty($id_classe) && !empty($id_matiere)) {
                //Récupération du nombre de matières d'une classe pour différer l'affichage en fonction
                $id_classe = $id_classe == -1 ? '' : $id_classe;
                $id_matiere = $id_matiere == -1 ? '' : $id_matiere;
                
                //Récupération des différents supports
                $class = new ClasseModel();
                $args = !empty($id_classe) ? array('id' => $id_classe) : array();
                $c = $class->search($args, array('orderby' => 'libelle', 'orderby_dir' => 'asc'));
                $classes = array();
                $m = new EnseignantsMatieresClassesModel();
                foreach ($c as $class) {
                    $class['nom_classe'] = $class['libelle'];
                    $class['id_classe'] = $class['id'];
                    
                    $args = array('id_classe' => $class['id']);
                    if (!empty($id_matiere)) $args['id_matiere'] = $id_matiere;
                    
                    $class['subjects'] = $m->search($args, array('orderby' => 'matieres.nom', 'orderby_dir' => 'asc'));
                    foreach ($class['subjects'] as &$subject) {
                        $m = new SupportModel();
                        $subject['supports'] = $m->search(array('id_classe' => $class['id'], 'id_matiere' => $subject['id_matiere']));
                    }
                    $classes[] = $class;
                }
                $v = new SupportDefaultView();
                $v->show(array('supports' => array('classes' => $classes), 'upload_path' => $this->configuration['path']['upload']));
            }
        }
    }
    
    public function doDelete($args) {
        $support_id = $args['support_id'];
        if (in_array($_SESSION['user']['privileges'], array('enseignant', 'superviseur'))) {
            $support = new SupportModel();
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['validation'])) {
                    $support->delete(array('id' => $support_id));
                }
                Router::redirect('SupportList'); // ajouter le retour à la bonne matière/classe pour le superviseur
            } else {
                $support->get($support_id);
                $v = new SupportDeleteView();
                $v->show($support->toArray());
            }
        }
    }
    
    /* Fonction pour ajouter un support */
    public function doAdd() {
        // Tableau du filtre des types de fichiers
        $non_types = array('application/octet-stream', "application/x-msdos-program");//, "application/vnd.openxmlformats-officedocument.wordprocessingml.document");
        
        if ($_SESSION['user']['privileges'] == 'enseignant') {
            // Cas de la validation d'ajout
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_FILES)) {
                if (isset($_POST['annulation'])) {
                    Router::redirect('SupportList');
                }
                // Erreurs de saisie
                if (empty($_POST['nom_support'])) {
                    $_SESSION['ERROR_MSG'] = 'Veuillez remplir le champ <b>Titre du support</b>';
                } else if ($_FILES['nom_du_fichier']['error']) {
                    switch ($_FILES['nom_du_fichier']['error']) {
                        case 1: // UPLOAD_ERR_INI_SIZE
                            $_SESSION['ERROR_MSG'] = 'Le fichier dépasse la limite autorisée par le serveur.';
                            break;
                        case 2: // UPLOAD_ERR_FORM_SIZE
                            $_SESSION['ERROR_MSG'] = 'Le fichier dépasse la limite autorisée dans le formulaire HTML.';
                            break;
                        case 3: // UPLOAD_ERR_PARTIAL
                            $_SESSION['ERROR_MSG'] = 'L\'envoi du fichier a été interrompu pendant le transfert.s';
                            break;
                        case 4: // UPLOAD_ERR_NO_FILE
                            $_SESSION['ERROR_MSG'] = 'Vous n\'avez uploadé aucun fichier.';
                            break;
                    }
                } else {
                    //On vérifie que le type du fichier est bien autorisé
                    if (in_array($_FILES['nom_du_fichier']['type'], $non_types)) {
                        $_SESSION['ERROR_MSG'] = "Il vous est interdit d'uploader ce type de fichier, inutile de changer l'extension.";
                    } else {
                        //Vérification de la présence des variables nécessaires
                        if (isset($_GET['class_id']) && isset($_GET['subject_id'])) {
                            // Envoi du fichier
                            $f = new FileManipulation();
                            // print($_POST['nom_support']);
                            $filename = $f->send($_POST['nom_support'], 'nom_du_fichier', $this->configuration['path']['upload']);
                            
                            // Préparation de l'insertion à la base : création du tableau contenant les données nécessaires, puis création de l'objet nécessaire à l'insertion
                            $support = new SupportModel(array(
                                'date_creation'     => date('Y-m-d H:i:s'),
                                'titre'             => $_POST['nom_support'],
                                'tags'              => $_POST['tags'],
                                'nom_fichier'       => $filename,
                                'id_enseignant'     => $_SESSION['user']['id'],
                                'id_matiere'        => $_GET['subject_id'],
                                'id_classe'         => $_GET['class_id']
                            ));
                            $support->save();
                        } else {
                            $_SESSION['ERROR_MSG'] = 'Certains paramètres manquent à l\'appel...';
                        }
                    }
                }
                //On vide d'éventuels fichiers temporaires résiduels en cas d'erreur
                unset($_FILES);
                if (!empty($_SESSION['ERROR_MSG'])) {
                    $parms = array(
                        'class'         =>$_GET['class_id'],
                        'subject'       =>$_GET['subject_id'],
                        'nom_support'   =>(isset($_POST['nom_support']) ? $_POST['nom_support'] : ''),
                        'tags'          =>(isset($_POST['tags']) ? $_POST['tags'] : '')
                    );
                    $v = new SupportAddView();
                    $v->show($parms);
                } else {
                    Router::redirect('SupportList');
                }
            } else {
                $parms = array(
                    'class'         => $_GET['class_id'],
                    'subject'       => $_GET['subject_id'],
                    'nom_support'   => isset($_POST['nom_support']) ? $_POST['nom_support'] : '',
                    'tags'          => isset($_POST['tags']) ? $_POST['tags'] : ''
                );
                $v = new SupportAddView();
                $v->show($parms);
            }
        } elseif ($_SESSION['user']['privileges'] == 'superviseur') {
            // Cas de la validation d'ajout
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_FILES)) {
                if (isset($_POST['annulation'])) {
                    Router::redirect('SupportList');
                }
                // Erreurs dues à l'utilisateur
                if (empty($_POST['nom_support'])) {
                    $_SESSION['ERROR_MSG'] = 'Veuillez remplir le champ <b>Titre du support</b>';
                } else if (empty($_POST['mat_prof'])) {
                    $_SESSION['ERROR_MSG'] = 'Veuillez choisir un enseignant dans la liste <b>Matière/Enseignant</b>';
                } else if ($_FILES['nom_du_fichier']['error']) {
                    switch ($_FILES['nom_du_fichier']['error']) {
                        // Erreurs d'upload
                        case 1: // UPLOAD_ERR_INI_SIZE
                            $_SESSION['ERROR_MSG'] = 'Le fichier dépasse la limite autorisée par le serveur.';
                            break;
                        case 2: // UPLOAD_ERR_FORM_SIZE
                            $_SESSION['ERROR_MSG'] = 'Le fichier dépasse la limite autorisée dans le formulaire HTML.';
                            break;
                        case 3: // UPLOAD_ERR_PARTIAL
                            $_SESSION['ERROR_MSG'] = 'L\'envoi du fichier a été interrompu pendant le transfert.s';
                            break;
                        case 4: // UPLOAD_ERR_NO_FILE
                            $_SESSION['ERROR_MSG'] = 'Vous n\'avez uploadé aucun fichier.';
                            break;
                    }
                } else {
                    // On vérifie que le type du fichier est bien autorisé
                    if (in_array($_FILES['nom_du_fichier']['type'], $non_types)) {
                        $_SESSION['ERROR_MSG'] = "Il vous est interdit d'uploader ce type de fichier, inutile de changer l'extension.";
                    } else {
                        // Vérification de la présence des variables nécessaires
                        if (isset($_GET['class'])) {
                            // Envoi du fichier
                            $f = new FileManipulation();
                            $filename = $f->send($_POST['nom_support'], 'nom_du_fichier', $this->configuration['path']['upload']);
                            
                            $mp = explode(";", $_POST['mat_prof']);
                            // Préparation de l'insertion à la base : création du tableau contenant les données nécessaires, puis création de l'objet nécessaire à l'insertion
                            $support = new SupportModel(array(
                                'date_creation' => date('Y-m-d H:i:s'),
                                'titre'         => $_POST['nom_support'],
                                'tags'          => $_POST['tags'],
                                'nom_fichier'   => $filename,
                                'id_enseignant' => $mp[1],
                                'id_matiere'    => $mp[0],
                                'id_classe'     => $_GET['class_id']
                            ));
                            $support->save();
                        } else {
                            $_SESSION['ERROR_MSG'] = 'Certains paramètres manquent à l\'appel...';
                        }
                    }
                }
                // On vide d'éventuels fichiers temporaires résiduels en cas d'erreur et on redirige en fonction
                unset($_FILES);
                if (!empty($_SESSION['ERROR_MSG'])) {
                    //Création de la liste matières/prof pour sélectionner le prof à qui on uploade le support
                    $m = new EnseignantsMatieresClassesModel();
                    $matiere = $m->search(array('id_classe' => $_GET['class']));
                    
                    $mat_prof = array();
                    foreach ($matiere as $m) {
                        $user = new UserModel();
                        $user->get($m['id_enseignant']);
                        
                        if (!isset($mat_prof[$m['id_matiere']])) {
                            $mat_prof[$m['id_matiere']] = $m;
                        }
                        if (!isset($mat_prof[$m['id_matiere']]['profs'][$user->id])) {
                            $mat_prof[$m['id_matiere']]['profs'][$user->id] = $user->toArray();
                        }
                    }
                    $parms = array(
                        'class'         => $_GET['class'],
                        'subject'       => $_GET['subject'],
                        'nom_support'   => (isset($_POST['nom_support']) ? $_POST['nom_support'] : ''),
                        'tags'          => (isset($_POST['tags']) ? $_POST['tags'] : ''),
                        'mat_prof'      => (isset($_POST['mat_prof']) ? $_POST['mat_prof'] : ''),
                        'matieres'      => $mat_prof
                    );
                    $v = new SupportAddView();
                    $v->show($parms);
                } else {
                    Router::redirect('SupportList');
                }
            } else {
                //Création de la liste matières/prof pour sélectionner le prof à qui on uploade le support
                $m = new EnseignantsMatieresClassesModel();
                $matiere = $m->search(array('id_classe' => $_GET['class']));
                
                $mat_prof = array();
                foreach ($matiere as $m) {
                    $user = new UserModel();
                    $user->get($m['id_enseignant']);
                    
                    if (!isset($mat_prof[$m['id_matiere']])) {
                        $mat_prof[$m['id_matiere']] = $m;
                    }
                    if (!isset($mat_prof[$m['id_matiere']]['profs'][$user->id])) {
                        $mat_prof[$m['id_matiere']]['profs'][$user->id] = $user->toArray();
                    }
                }
                $parms = array(
                    'class'         => $_GET['class_id'],
                    'nom_support'   => isset($_POST['nom_support']) ? $_POST['nom_support'] : '',
                    'tags'          => isset($_POST['tags']) ? $_POST['tags'] : '',
                    'matieres'      => $mat_prof,
                    'mat_prof'      => isset($_POST['mat_prof']) ? $_POST['mat_prof'] : ''
                );
                $v = new SupportAddView();
                $v->show($parms);
            }
        }
    }
    
    /* Fonction pour éditer le titre et les mots-clés d'un support */
    public function doEdit($args) {
        $support_id = $args['support_id'];
        if ($_SESSION['user']['privileges'] == 'enseignant') {
            $support = new SupportModel();
            $support->get($support_id);
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['validation'])) {
                    //Si on valide, on sépare la classe et la matière
                    $tab = explode(';', $_POST['id_classe_id_matiere']);
                    $f = new FileManipulation();
                    
                    //On formate le nom de fichier en fonction de nos désirs, puis on le renomme et on met à jour la base
                    $new_nom_fichier = $f->format($_POST['titre'], $_POST['nom_fichier']);
                    rename($this->configuration['path']['upload'] . $_POST['nom_fichier'], $this->configuration['path']['upload'] . $new_nom_fichier);
                    $support->titre             = $_POST['titre'];
                    $support->tags              = $_POST['tags'];
                    $support->nom_fichier       = $new_nom_fichier;
                    $support->id_enseignant     = $_SESSION['user']['id'];
                    $support->id_classe         = $tab[0];
                    $support->id_matiere        = $tab[1];
                    $support->save();
                }
                Router::redirect('SupportList');
            }
            
            //On récupère la liste des matières par classe, afin de permettre de déplacer le support d'une classe à l'autre
            $m = new EnseignantsMatieresClassesModel();
            $resc = $m->search(array('id_enseignant' => $_SESSION['user']['id']));
            $classes = array();
            foreach ($resc as $rc) {
                if (isset($classes[$rc['id_classe']])) {
                    continue;
                }
                $classes[$rc['id_classe']] = array('id' => $rc['id_classe'], 'libelle' => $rc['nom_classe']);
                $classes[$rc['id_classe']]['subjects'] = array();
                foreach ($resc as $subject) {
                    if ($subject['id_classe'] == $rc['id_classe']) {
                        $classes[$rc['id_classe']]['subjects'][$subject['id_matiere']] = array('id' => $subject['id_matiere'], 'nom' => $subject['nom_matiere']);
                    }
                }
            }
            $v = new SupportEditView();
            $parms = array(
                'id'            => $support_id,
                'tags'          => $support->tags,
                'titre'         => $support->titre,
                'nom_fichier'   => $support->nom_fichier,
                'classes'       => $classes,
                'matiere'       => $support->id_matiere,
                'classe'        => $support->id_classe
            );
            $v->show($parms);
        }
    }
}
?>
