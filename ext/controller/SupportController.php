<?php
class SupportController extends Controller {
    /* Fonction pour afficher la liste des supports */
    public function doList() {
        if ($_SESSION['user_privileges'] == 'enseignant') {
            // Recherche des diff�rentes classes de l'enseignant
            $m = new MatieresClasseModel();
            $classes = $m->getClassesEnseignant(array('id' => $_SESSION['user_id']));
            foreach ($classes as &$class) {
                // Recherche des diff�rentes mati�res de l'enseignant associ�es � la classe
                $resm = $this->dbo->query('select m.id, m.nom from enseignants_matieres_classes e, matieres m where e.id_classe=' . $class['id'] . ' and e.id_matiere=m.id and e.id_enseignant=' . $_SESSION['user_id'] . ' order by nom asc');
                $subjects = array();
                foreach ($resm as $rm) {
                    $subject = array('id' => $rm['id'], 'nom' => $rm['nom']);
                    
                    // Recherche des diff�rentes supports de l'enseignant associ�s � la mati�re et � la classe
                    $ress = $this->dbo->query('select s.id, date_format(s.date_creation, "%d/%m/%Y %Hh%i") as date, s.nom_fichier, s.titre from supports s where s.id_matiere=' . $rm['id'] . ' and s.id_classe=' . $class['id'] . ' and s.id_enseignant=' . $_SESSION['user_id'] . ' order by s.date_creation asc');
                    $supports = array();
                    foreach ($ress as $rs) {
                        $supports[] = array('id' => $rs['id'], 'date' => $rs['date'], 'nom_fichier' => $rs['nom_fichier'], 'titre' => $rs['titre']);
                    }
                    $subject['supports'] = $supports;
                    $subjects[] = $subject;
                }
                $class['subjects'] = $subjects;
            }
            $v = new SupportDefaultView();
            $v->show(array('classes' => $classes));
        } else if ($_SESSION['user_privileges'] == 'eleve') {
            // R�cup�ration de la liste des mati�res de la classe de l'�l�ve
            $matieres = $this->dbo->query('select distinct m.id, m.nom from enseignants_matieres_classes e, matieres m where e.id_classe=' . $_SESSION['user_class'] . ' and m.id=e.id_matiere order by nom');
            
            $id_matiere = $this->_getArg('id_matiere');
            $titre_on = $this->_getArg('titre_on');
            $mots_cles_on = $this->_getArg('mots_cles_on');
            $and = $this->_getArg('and');
            $mots_cles = $this->_getArg('mots_cles');
            // Pr�paration des diff�rentes donn�es utiles � l'affichage de la zone de recherche, puis affichage
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
                // Ajout du filtre sur la mati�re si elle existe
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
                
                // R�cup�ration des supports
                $subjects = $this->dbo->query('select distinct m.id, m.nom from matieres m, supports s where s.id_classe=' . $_SESSION['user_class'] . ' and s.id_matiere=m.id' . $whereMatiere . $where . ' order by nom asc');
                foreach ($subjects as &$subject) {
                    $supports = $this->dbo->query('select s.id, date_format(s.date_creation, "%d/%m/%Y %Hh%i") as date, concat(u.civility, " ",u.nom) as enseignant, s.nom_fichier, s.titre from supports s, utilisateurs u where s.id_enseignant=u.id and s.id_matiere=' . $subject['id'] . ' and s.id_classe=' . $_SESSION['user_class'] . $where . ' order by s.date_creation asc');
                    $subject['supports'] = $supports;
                }
                
                $classe = array('subjects' => $subjects);
                $classes = array($classe);
                $v = new SupportDefaultView();
                $v->show(array('classes' => $classes));
            }
        } else if ($_SESSION['user_privileges'] == 'superviseur') {
            // R�cup�ration des diff�rentes classes/mati�res auxquelles l'�l�ve a acc�s afin de lui en offrir la liste
            $m = new ClasseModel();
            $classes = $m->listing();
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
                //R�cup�ration du nombre de mati�res d'une classe pour diff�rer l'affichage en fonction
                $id_classe = $id_classe == -1 ? '' : $id_classe;
                $id_matiere = $id_matiere == -1 ? '' : $id_matiere;
                
                //R�cup�ration des diff�rents supports
                $classes = $this->dbo->query('select id, libelle from classes' . (!empty($id_classe) ? ' where id=' . $id_classe : '') . ' order by libelle asc');
                foreach ($classes as &$class) {
                    $class['subjects'] = $this->dbo->query('select emc.id_matiere as id, m.nom from enseignants_matieres_classes emc, matieres m where emc.id_matiere=m.id and emc.id_classe=' . $class['id'] . (!empty($id_matiere) ? ' and emc.id_matiere=' . $id_matiere : '') . ' order by m.nom asc');
                    foreach ($class['subjects'] as &$subject) {
                        $m = new SupportModel();
                        $subject['supports'] = $m->listing($class['id'], $subject['id']);
                    }
                }
                $v = new SupportDefaultView();
                $v->show(array('classes' => $classes));
            }
        }
    }
    
    /* Fonction pour supprimer un support */
    public function doDelete($args) {
        $support_id = $args['support_id'];
        if ($_SESSION['user_privileges'] == 'enseignant' || $_SESSION['user_privileges'] == 'superviseur') {
            $m = new SupportModel();
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['validation'])) {
                    /* Suppression du support dans la base */
                    $m->delete($support_id);
                }
                Router::redirect('SupportList'); // ajouter le retour � la bonne mati�re/classe pour le superviseur
            } else {
                /* R�cup�ration des informations associ�es au support dans la base */
                $r = $m->get(array("id" => $support_id));
                $v = new SupportDeleteView();
                $params = array(
                    'id'            => $support_id,
                    'classe'        => $r['classe'],
                    'matiere'       => $r['matiere'],
                    'titre'         => $r['titre'],
                    'nom_fichier'   => $r['nom_fichier']);
                $v->show($params);
            }
        }
    }
    
    /* Fonction pour ajouter un support */
    public function doAdd() {
        // Tableau du filtre des types de fichiers
        $non_types = array('application/octet-stream', "application/x-msdos-program");//, "application/vnd.openxmlformats-officedocument.wordprocessingml.document");
        
        if ($_SESSION['user_privileges'] == 'enseignant') {
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
                                $_SESSION['ERROR_MSG'] = 'Le fichier d�passe la limite autoris�e par le serveur.';
                                break;
                            case 2: // UPLOAD_ERR_FORM_SIZE
                                $_SESSION['ERROR_MSG'] = 'Le fichier d�passe la limite autoris�e dans le formulaire HTML.';
                                break;
                            case 3: // UPLOAD_ERR_PARTIAL
                                $_SESSION['ERROR_MSG'] = 'L\'envoi du fichier a �t� interrompu pendant le transfert.s';
                                break;
                            case 4: // UPLOAD_ERR_NO_FILE
                                $_SESSION['ERROR_MSG'] = 'Vous n\'avez upload� aucun fichier.';
                                break;
                        }
                } else {
                    //On v�rifie que le type du fichier est bien autoris�
                    if (in_array($_FILES['nom_du_fichier']['type'], $non_types)) {
                        $_SESSION['ERROR_MSG'] = "Il vous est interdit d'uploader ce type de fichier, inutile de changer l'extension.";
                    } else {
                        //V�rification de la pr�sence des variables n�cessaires
                        if (isset($_GET["class"]) && isset($_GET["subject"])) {
                            // Envoi du fichier
                            $f = new FileManipulation();
                            // print($_POST['nom_support']);
                            $filename = $f->send($_POST['nom_support'], 'nom_du_fichier', UPLOAD_PATH);
                            
                            // Pr�paration de l'insertion � la base : cr�ation du tableau contenant les donn�es n�cessaires, puis cr�ation de l'objet n�cessaire � l'insertion
                            $params = array(
                                'titre'             => $_POST['nom_support'],
                                'tags'              => $_POST['tags'],
                                'nom_fichier'       => $filename,
                                'id_enseignant'     => $_SESSION['user_id'],
                                'id_matiere'        => $_GET['subject'],
                                'id_classe'         => $_GET['class']
                            );
                            $s = new SupportModel();
                            $s->create($params);
                        } else {
                            $_SESSION['ERROR_MSG'] = 'Certains param�tres manquent � l\'appel...';
                        }
                    }
                }
                //On vide d'�ventuels fichiers temporaires r�siduels en cas d'erreur
                unset($_FILES);
                if (!empty($_SESSION['ERROR_MSG'])) {
                    $parms = array("class"=>$_GET["class"], "subject"=>$_GET["subject"], "nom_support"=>(isset($_POST['nom_support']) ? $_POST['nom_support'] : ''),"tags"=>(isset($_POST['tags']) ? $_POST['tags'] : ''));
                    $v = new SupportAddView();
                    $v->show($parms);
                } else {
                    header('Location: index.php?page=supports');
                }
            } else {
                $parms = array("class"=>$_GET["class"], "subject"=>$_GET["subject"], "nom_support"=>(isset($_POST['nom_support']) ? $_POST['nom_support'] : ''),"tags"=>(isset($_POST['tags']) ? $_POST['tags'] : ''));
                $v = new SupportAddView();
                $v->show($parms);
            }
        } else if ($_SESSION['user_privileges'] == 'superviseur') {
            // Cas de la validation d'ajout
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_FILES)) {
                if (isset($_POST['annulation'])) {
                    Router::redirect('SupportList');
                }
                // Erreurs dues � l'utilisateur
                if (empty($_POST['nom_support'])) {
                    $_SESSION['ERROR_MSG'] = 'Veuillez remplir le champ <b>Titre du support</b>';
                } else if (empty($_POST['mat_prof'])) {
                    $_SESSION['ERROR_MSG'] = 'Veuillez choisir un enseignant dans la liste <b>Mati�re/Enseignant</b>';
                } else if ($_FILES['nom_du_fichier']['error']) {
                    switch ($_FILES['nom_du_fichier']['error']) {
                        // Erreurs d'upload
                        case 1: // UPLOAD_ERR_INI_SIZE
                            $_SESSION['ERROR_MSG'] = 'Le fichier d�passe la limite autoris�e par le serveur.';
                            break;
                        case 2: // UPLOAD_ERR_FORM_SIZE
                            $_SESSION['ERROR_MSG'] = 'Le fichier d�passe la limite autoris�e dans le formulaire HTML.';
                            break;
                        case 3: // UPLOAD_ERR_PARTIAL
                            $_SESSION['ERROR_MSG'] = 'L\'envoi du fichier a �t� interrompu pendant le transfert.s';
                            break;
                        case 4: // UPLOAD_ERR_NO_FILE
                            $_SESSION['ERROR_MSG'] = 'Vous n\'avez upload� aucun fichier.';
                            break;
                    }
                } else {
                    // On v�rifie que le type du fichier est bien autoris�
                    if (in_array($_FILES['nom_du_fichier']['type'], $non_types)) {
                        $_SESSION['ERROR_MSG'] = "Il vous est interdit d'uploader ce type de fichier, inutile de changer l'extension.";
                    } else {
                        // V�rification de la pr�sence des variables n�cessaires
                        if (isset($_GET['class'])) {
                            // Envoi du fichier
                            $f = new FileManipulation();
                            $filename = $f->send($_POST['nom_support'], 'nom_du_fichier', UPLOAD_PATH);
                            
                            $mp = explode(";", $_POST['mat_prof']);
                            // Pr�paration de l'insertion � la base : cr�ation du tableau contenant les donn�es n�cessaires, puis cr�ation de l'objet n�cessaire � l'insertion
                            $params = array(
                                'titre' => $_POST['nom_support'],
                                'tags' => $_POST['tags'],
                                'nom_fichier' => $filename,
                                'id_enseignant' => $mp[1],
                                'id_matiere' => $mp[0],
                                'id_classe' => $_GET['class']
                            );
                            $s = new SupportModel();
                            $s->create($params);
                        } else {
                            $_SESSION['ERROR_MSG'] = 'Certains param�tres manquent � l\'appel...';
                        }
                    }
                }
                // On vide d'�ventuels fichiers temporaires r�siduels en cas d'erreur et on redirige en fonction
                unset($_FILES);
                if(!empty($_SESSION['ERROR_MSG'])) {
                    //Cr�ation de la liste mati�res/prof pour s�lectionner le prof � qui on uploade le support
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
                //Cr�ation de la liste mati�res/prof pour s�lectionner le prof � qui on uploade le support
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
                $parms = array("class"=>$_GET["class"], "nom_support"=>(isset($_POST['nom_support']) ? $_POST['nom_support'] : ''),"tags"=>(isset($_POST['tags']) ? $_POST['tags'] : ''), "matieres"=>$mat_prof, "mat_prof"=>(isset($_POST["mat_prof"]) ? $_POST["mat_prof"] : ''));
                $v = new SupportAddView();
                $v->show($parms);
            }
        }
    }
    
    /* Fonction pour �diter le titre et les mots-cl�s d'un support */
    public function doEdit($args) {
        $support_id = $args['support_id'];
        if ($_SESSION['user_privileges'] == 'enseignant') {
            $m = new SupportModel();
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['annulation'])) {
                    Router::redirect('SupportList');
                }
                
                /* Gestion des erreurs */
                if(empty($_POST['titre'])) {
                    $_SESSION['ERROR_MSG'] = 'Veuillez remplir le champ <b>Titre</b>';
                }
                /* Fin de la gestion des erreurs */
                
                if (!isset($_SESSION['ERROR_MSG'])) {
                    if (isset($_POST['validation'])) {
                        //Si on valide, on s�pare la classe et la mati�re
                        $tab = explode(';', $_POST['id_classe_id_matiere']);
                        $f = new FileManipulation();
                        
                        //On formate le nom de fichier en fonction de nos d�sirs, puis on le renomme et on met � jour la base
                        $new_nom_fichier = $f->format($_POST['titre'], $_POST['nom_fichier']);
                        rename(UPLOAD_PATH . $_POST['nom_fichier'], UPLOAD_PATH . $new_nom_fichier);
                        $params = array(
                            'titre'                 => $_POST['titre'],
                            'tags'                  => $_POST['tags'],
                            'nom_fichier'           => $new_nom_fichier,
                            'id_enseignant'         => $_SESSION['user_id'],
                            'id_classe'             => $tab[0],
                            'id_matiere'            => $tab[1],
                        );
                        $m->update($support_id, $params);
                    }
                    Router::redirect('SupportList');
                }
            }
            //r�cup�ration des informations sur le support � modifier
            $r = $m->get(array('id' => $support_id));
            
            //On r�cup�re la liste des mati�res par classe, afin de permettre de d�placer le support d'une classe � l'autre
            $classes = array();
            $resc = $this->dbo->query('select distinct c.id, c.libelle from enseignants_matieres_classes e, classes c where e.id_classe=c.id and e.id_enseignant=' . $_SESSION['user_id'] . ' order by libelle asc');
            foreach ($resc as $rc) {
                $class = array('id' => $rc['id'], 'libelle' => $rc['libelle']);
                $resm = $this->dbo->query('select m.id, m.nom from enseignants_matieres_classes e, matieres m where e.id_classe=' . $rc['id'] . ' and e.id_matiere=m.id and e.id_enseignant=' . $_SESSION['user_id'] . ' order by nom asc');
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
