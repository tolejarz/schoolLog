<?php
class SupportController extends Controller {
    public function doList() {
        if ($_SESSION['user']['privileges'] == 'enseignant') {
            // Recherche des différentes classes de l'enseignant
            $m = new EnseignantsMatieresClassesModel();
            $classes = $m->search(array('id_enseignant' => $_SESSION['user']['id']));
            foreach ($classes as &$class) {
                // Recherche des différentes matières de l'enseignant associées à la classe
                $resm = $m->search(array('id_classe' => $class['id_classe'], 'id_enseignant' => $_SESSION['user']['id']));
                
                $subjects = array();
                $support = new SupportModel();
                foreach ($resm as $rm) {
                    $subject = array('id' => $rm['id_matiere'], 'nom' => $rm['nom_matiere']);
                    
                    // Recherche des différentes supports de l'enseignant associés à la matière et à la classe
                    $supports = $support->search(array('id_matiere' => $rm['id_matiere'], 'id_classe' => $class['id_classe'], 'id_enseignant' => $_SESSION['user']['id']));
                    $subject['supports'] = $supports;
                    $subjects[] = $subject;
                }
                $class['subjects'] = $subjects;
            }
            $v = new SupportDefaultView();
            $v->show(array('classes' => $classes));
        } elseif ($_SESSION['user']['privileges'] == 'eleve') {
            // Récupération de la liste des matières de la classe de l'élève
            $matieres = $this->dbo->query('select distinct m.id, m.nom from enseignants_matieres_classes e, matieres m where e.id_classe=' . $_SESSION['user']['class'] . ' and m.id=e.id_matiere order by nom');
            
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
                $subjects = $this->dbo->query('select distinct m.id, m.nom from matieres m, supports s where s.id_classe=' . $_SESSION['user']['class'] . ' and s.id_matiere=m.id' . $whereMatiere . $where . ' order by nom asc');
                foreach ($subjects as &$subject) {
                    $supports = $this->dbo->query('select s.id, date_format(s.date_creation, "%d/%m/%Y %Hh%i") as date, concat(u.civility, " ",u.nom) as enseignant, s.nom_fichier, s.titre from supports s, utilisateurs u where s.id_enseignant=u.id and s.id_matiere=' . $subject['id'] . ' and s.id_classe=' . $_SESSION['user']['class'] . $where . ' order by s.date_creation asc');
                    $subject['supports'] = $supports;
                }
                
                $classe = array('subjects' => $subjects);
                $classes = array($classe);
                $v = new SupportDefaultView();
                $v->show(array('classes' => $classes));
            }
        } elseif ($_SESSION['user']['privileges'] == 'superviseur') {
            // Récupération des différentes classes/matières auxquelles l'élève a accès afin de lui en offrir la liste
            $class = new ClasseModel();
            $classes = $class->search();
            foreach ($classes as &$c) {
                $c['matieres'] = $this->dbo->query('select distinct m.id, m.nom from enseignants_matieres_classes e, matieres m where e.id_classe=' . $c['id'] . ' and m.id=e.id_matiere order by nom');
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
                $v->show(array('classes' => $classes));
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
                            $filename = $f->send($_POST['nom_support'], 'nom_du_fichier', UPLOAD_PATH);
                            
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
                            $filename = $f->send($_POST['nom_support'], 'nom_du_fichier', UPLOAD_PATH);
                            
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
                if(!empty($_SESSION['ERROR_MSG'])) {
                    //Création de la liste matières/prof pour sélectionner le prof à qui on uploade le support
                    $msql = $this->dbo->query('select distinct m.id, m.nom from matieres m, enseignants_matieres_classes e where e.id_classe="'.$_GET["class"].'" and m.id=e.id_matiere order by 2');
                    $mat_prof = array();
                    foreach ($msql as $m) {
                        $matiere = array("id" => $m['id'], 'nom' => $m["nom"]);
                        $psql = $this->dbo->query('select distinct u.id, u.nom from enseignants_matieres_classes e, utilisateurs u where e.id_classe="'.$_GET["class"].'" and e.id_matiere="'.$m['id'].'" and u.id=e.id_enseignant order by 2');
                        foreach ($psql as $p) {
                            $profs[] = array("id" => $p["id"], 'nom'=> $p['nom']);
                        }
                        if (isset($profs)) {
                            $matiere['profs'] = $profs;
                            $mat_prof[] = $matiere;
                            $profs = null;
                        }
                    }
                    $parms = array("class"=>$_GET["class"], "subject"=>$_GET["subject"], "nom_support"=>(isset($_POST['nom_support']) ? $_POST['nom_support'] : ''),"tags"=>(isset($_POST['tags']) ? $_POST['tags'] : ''),"mat_prof"=> (isset($_POST['mat_prof']) ? $_POST['mat_prof'] : ''),  "matieres"=>$mat_prof);
                    $v = new SupportAddView();
                    $v->show($parms);
                } else {
                    Router::redirect('SupportList');
                }
            } else {
                //Création de la liste matières/prof pour sélectionner le prof à qui on uploade le support
                $msql = $this->dbo->query('select distinct m.id, m.nom from matieres m, enseignants_matieres_classes e where e.id_classe="'.$_GET["class"].'" and m.id=e.id_matiere order by 2');
                $mat_prof = array();
                foreach ($msql as $m) {
                    $matiere = array("id" => $m['id'], 'nom' => $m["nom"]);
                    $psql = $this->dbo->query('select distinct u.id, u.nom from enseignants_matieres_classes e, utilisateurs u where e.id_classe="'.$_GET["class"].'" and e.id_matiere="'.$m['id'].'" and u.id=e.id_enseignant order by 2');
                    foreach ($psql as $p) {
                        $profs[] = array("id" => $p["id"], 'nom'=> $p['nom']);
                    }
                    
                    if (isset($profs)) {
                        $matiere['profs'] = $profs;
                        $mat_prof[] = $matiere;
                        $profs = null;
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
                    rename(UPLOAD_PATH . $_POST['nom_fichier'], UPLOAD_PATH . $new_nom_fichier);
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
            $classes = array();
            $resc = $this->dbo->query('select distinct c.id, c.libelle from enseignants_matieres_classes e, classes c where e.id_classe=c.id and e.id_enseignant=' . $_SESSION['user']['id'] . ' order by libelle asc');
            foreach ($resc as $rc) {
                $class = array('id' => $rc['id'], 'libelle' => $rc['libelle']);
                $resm = $this->dbo->query('select m.id, m.nom from enseignants_matieres_classes e, matieres m where e.id_classe=' . $rc['id'] . ' and e.id_matiere=m.id and e.id_enseignant=' . $_SESSION['user']['id'] . ' order by nom asc');
                $subjects = array();
                foreach ($resm as $rm) {
                    $subject = array('id' => $rm['id'], 'nom' => $rm['nom']);
                    $subjects[] = $subject;
                }
                $class['subjects'] = $subjects;
                $classes[] = $class;
            }
            $v = new SupportEditView();
            $parms = array(
                'id'            => $support_id,
                'tags'          => $r['tags'],
                'titre'         => $r['titre'],
                'nom_fichier'   => $r['nom_fichier'],
                'classes'       => $classes,
                'matiere'       => $r['matiere'],
                'classe'        => $r['classe']
            );
            $v->show($parms);
        }
    }
}
?>
