<?php
class CalendrierController extends Controller {
    public function doShow() {
        $week = $this->_getArg('week');
        $week = !empty($week) ? $week : (date('w') % 6 == 0 ? date('W') + 1 : date('W'));
        
        $dates = week_dates($week - 1, $week >= LAST_WEEK ? CURRENT_PROMOTION : CURRENT_PROMOTION + 1);
        $start = $dates[0];
        $end = $dates[4];
        
        $r = array();
        if ($_SESSION['user_privileges'] == 'eleve') {
            $m = new CalendrierModel();
            $r['jours'] = $m->get(array('id_eleve' => $_SESSION['user_id'], 'start' => $start, 'end' => $end,  'viewer_type' => $_SESSION['user_privileges']));
        } else if ($_SESSION['user_privileges'] == 'enseignant') {
            $m = new CalendrierModel();
            $r['jours'] = $m->get(array('id_enseignant' => $_SESSION['user_id'], 'start' => $start, 'end' => $end, 'viewer_type' => $_SESSION['user_privileges']));
            $r['_displayClasses'] = true;
            $r['_blockDnD'] = true;
        }
        $r['_arg'] = '&amp;action=' . $this->_getArg('action');
        $r['_week'] = $week;
        $v = new CalendrierDefaultView();
        $v->show($r);
    }
    
    public function doClass() {
        if (in_array($_SESSION['user_privileges'], array('enseignant', 'superviseur'))) {
            /* récupérations des classes */
            $classes = $this->dbo->query('select * from classes order by libelle asc');
            
            /* on récupère l'id de la classe à afficher. S'il n'existe pas, on affiche par défaut la première classe existante */
            $id_classe = $this->_getArg('id_classe');
            if (empty($id_classe) && !empty($classes)) {
                $id_classe = $classes[0]['id'];
            }
            
            $v = new CalendrierClassesSelectView();
            $v->show(array('classes' => $classes, 'id_classe' => $id_classe));
            
            if (!empty($id_classe)) {
                /* récupération des paramètres */
                $week = $this->_getArg('week');
                $week = !empty($week) ? $week : (date('w') % 6 == 0 ? date('W') + 1 : date('W'));
                
                $dates = week_dates($week - 1, $week >= LAST_WEEK ? CURRENT_PROMOTION : CURRENT_PROMOTION + 1);
                $start = $dates[0];
                $end = $dates[4];
                
                $r = array();
                $m = new CalendrierModel();
                $r['jours'] = $m->get(array('id_classe' => $id_classe, 'start' => $start, 'end' => $end, 'viewer_type' => $_SESSION['user_privileges']));
                $r['_arg'] = '&amp;action=' . $this->_getArg('action') . '&amp;id_classe=' . $this->_getArg('id_classe');
                $r['_week'] = $week;
                $v = new CalendrierDefaultView();
                $v->show($r);
            }
        }
    }
    
    public function doTeacher() {
        if (in_array($_SESSION['user_privileges'], array('enseignant', 'superviseur'))) {
            $week = $this->_getArg('week');
            $week = !empty($week) ? $week : (date('w') % 6 == 0 ? date('W') + 1 : date('W'));
            
            $dates = week_dates($week - 1, $week >= LAST_WEEK ? CURRENT_PROMOTION : CURRENT_PROMOTION + 1);
            $start = $dates[0];
            $end = $dates[4];
            
            /* Récupération de la liste des enseignants dans la base */
            $m = new UserModel();
            $enseignants = $m->listingEnseignants();
            
            $id_enseignant = $this->_getArg('id_enseignant');
            if (empty($id_enseignant)) {
                $id_enseignant = $enseignants[0]['id'];
            }
            
            $v = new CalendrierEnseignantsSelectView();
            $v->show(array('enseignants' => $enseignants, 'id_enseignant' => $id_enseignant));
            
            if (!empty($id_enseignant)) {
                $m = new CalendrierModel();
                $r['jours'] = $m->get(array('id_enseignant' => $id_enseignant, 'start' => $start, 'end' => $end, 'viewer_type' => $_SESSION['user_privileges']));
                $r['_arg'] = '&amp;action=' . $this->_getArg('action') . '&amp;id_enseignant=' . $this->_getArg('id_enseignant');
                $r['_week'] = $week;
                $r['_displayClasses'] = true;
                $v = new CalendrierDefaultView();
                $v->show($r);
            }
        }
    }
    
    public function doRequestAdd() {
        if (in_array($_SESSION['user_privileges'], array('enseignant', 'superviseur'))) {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                /* si l'utilisateur n'a pas cliqué sur Validation alors on annule et redirige */
                if (!isset($_POST['validation'])) {
                    Router::redirect('CalendarRequestList');
                }
                
                /* récupération et formatage des heures */
                $heure_origine = trim(sprintf('%02d:%02d', $_POST['heure_origine_h'], $_POST['heure_origine_m']));
                if ($_POST['hasDateReport'] == 'no') {
                    $heure_report = null;
                } else {
                    $heure_report = trim(sprintf('%02d:%02d', $_POST['heure_report_h'], $_POST['heure_report_m']));
                }
                
                $date_origine_timestamp = mktime(0, 0, 0, substr($_POST['date_origine'], 3, 2),  substr($_POST['date_origine'], 0, 2), substr($_POST['date_origine'], 6, 4));
                if ($_POST['hasDateReport'] == 'yes') {
                    $date_report_timestamp = mktime(0, 0, 0, substr($_POST['date_report'], 3, 2),  substr($_POST['date_report'], 0, 2), substr($_POST['date_report'], 6, 4));
                }
                
                /* tests d'erreurs */
                if (empty($_POST['date_origine'])) {
                    $_SESSION['ERROR_MSG'] = 'Veuillez saisir une date d\'origine';
                } else if (date('w', $date_origine_timestamp) % 6 == 0) {
                    $_SESSION['ERROR_MSG'] = 'Veuillez choisir un jour différent de samedi ou dimanche pour la date d\'origine';
                } else if (($_POST['hasDateReport'] == 'yes') && empty($_POST['date_report'])) {
                    $_SESSION['ERROR_MSG'] = 'Veuillez saisir une date de report';
                } else if (($_POST['hasDateReport'] == 'yes') && (date('w', $date_report_timestamp) % 6 == 0)) {
                    $_SESSION['ERROR_MSG'] = 'Veuillez choisir un jour différent de samedi ou dimanche pour la date de report';
                } else {
                    $m = new CalendrierModel();
                    
                    /* recherche du cours ayant lieu à $date_rigine $heure_origine */
                    $parms = array(
                        'id_classe'         => $_POST['id_classe'],
                        'date_origine'      => $this->FormatDateTimeFrToUs(trim($_POST['date_origine']), false),
                        'heure_origine'     => $this->FormatTimeFrToUs($heure_origine)
                    );
                    if ($_SESSION['user_privileges'] == 'enseignant') {
                        $parms['id_enseignant'] = $_SESSION['user_id'];
                    }
                    $id_cours = $m->getCours($parms);
                    
                    /* si un cours a été trouvé à la date/heure indiquée */
                    if (!empty($id_cours)) {
                        $sql2 = $this->dbo->query("
                            select
                                id
                            from
                                modele_planning
                            where
                                id not in (select id_modele_planning from operations)
                                and date_format(STR_TO_DATE('" . $_POST['date_report'] . "', '%d%m%Y'), '%w')=(jour-1)
                                and (
                                    (STR_TO_DATE('" . $heure_report . "', '%H%i')>=heure_debut and STR_TO_DATE('" . $heure_report . "', '%H%i')<heure_fin)
                                    or (
                                        time(time(STR_TO_DATE('" . $heure_report . "', '%H%i')) + TIMEDIFF((select heure_fin from modele_planning where id='" . $id_cours . "'), time(STR_TO_DATE('" . $heure_origine . "', '%H%i')))) > heure_debut
                                        and time(time(STR_TO_DATE('" . $heure_report . "', '%H%i')) + TIMEDIFF((select heure_fin from modele_planning where id='" . $id_cours . "'), time(STR_TO_DATE('" . $heure_origine . "', '%H%i'))))<=heure_fin
                                        )
                                    )
                            limit 0,1
                        UNION
                            SELECT
                                m.id
                            FROM
                                modele_planning m, operations o
                            WHERE
                                m.id=o.id_modele_planning
                                AND STR_TO_DATE('" . $_POST['date_report'] . "','%d%m%Y')=date(date_report)
                                AND ((STR_TO_DATE('" . $heure_report . "','%H%i') >= time(date_report) AND STR_TO_DATE('" . $heure_report . "','%H%i') < time(time(date_report) + TIMEDIFF(heure_fin,heure_debut)))
                                OR (time(STR_TO_DATE('" . $heure_report . "','%H%i')+TIMEDIFF((select heure_fin from modele_planning where id='" . $id_cours . "'), time(STR_TO_DATE('" . $heure_origine . "', '%H%i')))) > time(date_report) AND time(STR_TO_DATE('" . $heure_report . "', '%H%i')+TIMEDIFF((select heure_fin from modele_planning where id='" . $id_cours . "'), time(STR_TO_DATE('" . $heure_origine . "', '%H%i')))) <= time(time(date_report)+TIMEDIFF(heure_fin,heure_debut))))
                            LIMIT 0,1");
                        
                        /* si aucun cours ne se chevauchent */
                        if (empty($sql2)) {
                            /* création de la demande */
                            $parms = array(
                                'date_origine'              => $this->FormatDateTimeFrToUs(trim($_POST['date_origine']), false),
                                'date_report'               => $this->FormatDateTimeFrToUs($_POST['date_report'] . ' ' . $heure_report),
                                'id_enseignant'             => in_array('enseignant', $_SESSION['user_privileges']) ? $_SESSION['user_id'] : $this->dbo->sqleval('select id_enseignant from modele_planning where id=' . $id_cours),
                                'id_modele_planning'        => $id_cours
                            );
                            $m = new CalendrierModel();
                            $id_operation = $m->createOperation($parms);
                            
                            /* si la demande a bien été créée */
                            if ($id_operation) {
                                /* envoi du mail aux superviseurs */
                                if ($_SESSION['user_privileges'] == 'enseignant') {
                                    /* récupération des e-mails de tous les superviseurs */
                                    $ress = $this->dbo->query('select email from utilisateurs where find_in_set("superviseur", droits) > 0');
                                    $emails = array();
                                    foreach ($ress as $superviseur) {
                                        $emails[] = $superviseur['email'];
                                    }
                                    $emails = implode(';', $emails);
                                    
                                    /* on re-récupère la matière qu'on vient de créer */
                                    $r = $m->getOperation(array('id' => $id_operation));
                                    
                                    $v = new DemandeEmailView();
                                    
                                    $m = new Mail();
                                    //Demande : 1
                                    $m->SendMail($_SESSION['user_email'], $emails, 1, $v->show($r));
                                }
                                Router::redirect('CalendarRequestList');
                            } else {
                                $_SESSION['ERROR_MSG'] = 'La demande de modification n\'a pas pu être créée';
                            }
                        } else {
                            $_SESSION['ERROR_MSG'] = 'Un cours a déjà lieu aux horaires indiqués.';
                        }
                    } else {
                        $_SESSION['ERROR_MSG'] = 'Le cours demandé n\'existe pas';
                    }
                }
            }
            $m = new ClasseModel();
            $classes = $m->listing();
            
            $v = new CalendrierDemandeAddView();
            $v->show(array('classes' => $classes));
        }
    }
    
    
    function _doDragDrop() {
        if (in_array($_SESSION['user_privileges'], array('enseignant', 'superviseur'))) {
            $days = array(
                1                                       => 'lundi',
                1 + 1 * (SUBJECT_WIDTH + 6 + 1)         => 'mardi',
                1 + 2 * (SUBJECT_WIDTH + 6 + 1)         => 'mercredi',
                1 + 3 * (SUBJECT_WIDTH + 6 + 1)         => 'jeudi',
                1 + 4 * (SUBJECT_WIDTH + 6 + 1)         => 'vendredi'
            );
            
            $ids = explode(':', $_POST['id']);
            $datesSemaine = explode('-', $ids[2]);
            $first_token = explode('-', $ids[0]);
            
            /* nouvelle heure de début du cours */
            $heure_report_m = (($_POST['top'] / (PART_HEIGHT + 1)) * 30 + 8 * 60 + 30) % 60;
            $heure_report_h = ((($_POST['top'] / (PART_HEIGHT + 1)) * 30 + 8 * 60 + 30) - $heure_report_m) / 60;
            $heure_report = sprintf('%02d:%02d', $heure_report_h, $heure_report_m);
            
            $parms = array(
                'jour_report'           => $days[$_POST['left']],
                'heure_report'          => $this->FormatTimeFrToUs($heure_report),
                'jour'                  => $ids[1],
                'id_enseignant'         => $_SESSION['user_id'],
                'id_modele_planning'    => $first_token[0],
                'date_origine'          => date('Y-m-d', $datesSemaine[$ids[1]]),
                'date_report'           => date('Y-m-d', $datesSemaine[array_search($_POST['left'] , array_keys($days))]),
                'etat'                  => $_SESSION['user_privileges'] == 'superviseur' ? 'validée' : 'en attente'
            );
            
            $id_matiere = $this->dbo->sqleval('select "ok" from modele_planning where id=' . $parms['id_modele_planning'] . ' and id_enseignant=' . $parms['id_enseignant']);
            
            $r = array();
            if (($_SESSION['user_privileges'] == 'enseignant') && empty($id_matiere)) {
                $r['result'] = 'not-created';
            } else {
                $m = new CalendrierModel();
                // cas mise à jour de la demande existante
                if (count($first_token) == 2) {
                    $r = $m->updateOperation($first_token[1], $parms);
                    if ($r === null) {
                        $heure_report_fin = $this->dbo->sqleval('select date_format(mp.heure_fin, "%H:%i") from modele_planning mp where mp.id=' . $first_token[0]);
                        $r = array(
                            'result'                => 'deleted',
                            'id'                    => $first_token[0] . ':' . array_search($_POST['left'], array_keys($days)) . ':' . $ids[2],
                            'heureReport'           => $heure_report,
                            'heureReportFin'        => $heure_report_fin,
                            'etat'                  => ''
                        );
                    } else {
                        $heure_report_fin = $this->dbo->sqleval('select date_format(addtime(o.date_report, timediff(mp.heure_fin, mp.heure_debut)), "%H:%i") from modele_planning mp, operations o where o.id_modele_planning=mp.id and o.id=' . $first_token[1]);
                        $r = array(
                            'result'                => 'updated',
                            'id'                    => $ids[0] . ':' . array_search($_POST['left'], array_keys($days)) . ':' . $ids[2],
                            'heureReport'           => $heure_report,
                            'heureReportFin'        => $heure_report_fin,
                            'etat'                  => utf8_encode($parms['etat'])
                        );
                    }
                // cas création de la demande
                } else if (count($first_token) == 1) {
                    $id = $m->createOperation($parms);
                    if ($id != null) {
                        $heure_report_fin = $this->dbo->sqleval('select date_format(addtime(o.date_report, timediff(mp.heure_fin, mp.heure_debut)), "%H:%i") from modele_planning mp, operations o where o.id_modele_planning=mp.id and o.id=' . $id);
                        $r = array(
                            'result'                => 'created',
                            'id'                    => $first_token[0] . '-' . $id . ':' . array_search($_POST['left'], array_keys($days)) . ':' . $ids[2],
                            'dateOrigine'           => date('d/m/Y', $datesSemaine[$ids[1]]),
                            'heureReport'           => $heure_report,
                            'heureReportFin'        => $heure_report_fin,
                            'etat'                  => utf8_encode($parms['etat'])
                        );
                    } else {
                        $r['result'] = 'not-created';
                    }
                }
            }
        } else {
            $r['result'] = 'ungranted-user';
        }
        
        if (!in_array($r['result'], array('ungranted-user', 'not-created'))) {
            /* envoi du mail aux superviseurs */
            if ($_SESSION['user_privileges'] == 'enseignant') {
                // récupération des adresses e-mail des superviseurs
                $ress = $this->dbo->query('select email from utilisateurs where find_in_set("superviseur", droits) > 0');
                $emails = array();
                foreach ($ress as $superviseur) {
                    $emails[] = $superviseur['email'];
                }
                $emails = implode(';', $emails);
                
                // récupération de l'opération qu'on vient de créer
                $m = new CalendrierModel();
                $op = $m->getOperation(array('id' => ($r['result'] == 'created' ? $id : $first_token[1])));
                
                $v = new DemandeEmailView();
                
                $m = new Mail();
                $m->SendMail($_SESSION['user_email'], $emails, 'deplacement_cours', $v->show($op));
            }
        }
        
        header('Content-type: text/json; charset=utf-8');
        echo json_encode($r);
        die();
    }
    
    public function doRequestList() {
        $m = new CalendrierModel();
        $parms = array();
        $parms['demandes'] = $m->listingOperations($_SESSION['user_privileges'] == 'enseignant' ? array('id_enseignant' => $_SESSION['user_id']) : array());
        $v = new CalendrierDemandeView();
        $v->show($parms);
    }
    
    public function doRequestReject($args) {
        $request_id = $args['request_id'];
        if (in_array($_SESSION['user_privileges'], array('enseignant', 'superviseur'))) {
            $m = new CalendrierModel();
            $r = $m->getOperation(array('id' => $request_id));
            
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['annulation'])) {
                    Router::redirect('CalendarRequestList');
                }
                /* on passe l'état de la demande à "refusée" */
                if ($m->updateOperation($request_id, array('etat' => 'refusée')) > 0) {
                    /* on envoie l'e-mail de refus à l'enseignant (e-mail type 3) */
                    $v = new DemandeEmailView();
                    
                    $m = new Mail();
                    $m->SendMail($_SESSION['user_email'], $r['enseignant_email'], 3, $v->show($r));
                }
                $ajax = $this->_getArg('ajax');
                if (!empty($ajax)) {
                    die();
                }
                Router::redirect('CalendarRequestList');
            } else {
                $v = new CalendrierDemandeRejectView();
                $v->show($r);
            }
        }
    }
    
    public function doRequestAccept($args) {
        $request_id = $args['request_id'];
        if (in_array($_SESSION['user_privileges'], array('enseignant', 'superviseur'))) {
            $ajax = $this->_getArg('ajax');
            
            $m = new CalendrierModel();
            $r = $m->getOperation(array('id' => $request_id));
            
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['annulation'])) {
                    Router::redirect('CalendarRequestList');
                }
                
                if (empty($_POST['date_report']) && empty($ajax)) {
                    $_SESSION['ERROR_MSG'] = 'Veuillez saisir une date de report';
                } else {
                    $params = array();
                    $params['etat'] = 'validée';
                    if (empty($ajax)) {
                        $params['date_report'] = $this->FormatDateTimeFrToUs($_POST['date_report'] . ' ' . sprintf('%02d', $_POST['heure_report_h']) . ':' . sprintf('%02d', $_POST['heure_report_m']));
                    }
                    if ($m->updateOperation($request_id, $params) > 0) {
                        $m = new CalendrierModel();
                        $r = $m->getOperation(array('id' => $request_id));
                        
                        $v = new DemandeEmailView();
                        
                        $m = new Mail();
                        $m->SendMail($_SESSION['user_email'], $r['enseignant_email'], 2, $v->show($r));
                        $m->SendMail($_SESSION['user_email'], $r['classe_email'], 4, $v->show($r));
                    }
                    Router::redirect('CalendarRequestList');
                }
            }
            $v = new CalendrierDemandeAcceptView();
            $v->show($r);
        }
    }
    
    public function doRequestHistory() {
        if (in_array($_SESSION['user_privileges'], array('enseignant', 'superviseur'))) {
            /* Récupération du mois passé en paramètre, s'il y en a pas, on prend le mois courant */
            $month = $this->_getArg('month');
            if (empty($month)) {
                $month = date('m');
            }
            $year = $this->_getArg('year');
            if (empty($year)) {
                $year = date('Y');
            }
            $now = mktime(0, 0, 0, $month, 1, $year);
            
            /* Mois précédent / mois suivant */
            $prevMonthTimestamp = strtotime('-1 month', $now);
            $nextMonthTimestamp = strtotime('+1 month', $now);
            
            /* Si l'utilisateur est un enseignant, on n'affiche que ses propres réservations */
            $whereEnseignant = '';
            if ($_SESSION['user_privileges'] == 'enseignant') {
                $whereEnseignant = ' and u.id=' . $_SESSION['user_id'];
            }
            $demandes= $this->dbo->query('
                select 
                c.libelle as classe,
                m.nom as matiere,
                o.etat,
                u.nom as enseignant,
                o.id,
                o.date_origine,
                mp.heure_debut,
                o.date_report
            from
                modele_planning mp,
                operations o,
                classes c,
                matieres m,
                utilisateurs u
            where
                m.id=mp.id_matiere
                and o.id_modele_planning=mp.id
                and c.id=mp.id_classe
                and u.id=o.id_enseignant
                and (o.date_report is null or o.date_report < now())
                and date_format(o.date_report, "%Y%m")=' . date('Ym', $now) . 
                    $whereEnseignant . '
            order by
                o.date_creation desc');
            $params = array(
                'nextMonth'     => date('m', $nextMonthTimestamp),
                'prevMonth'     => date('m', $prevMonthTimestamp),
                'nextYear'      => date('Y', $nextMonthTimestamp),
                'prevYear'      => date('Y', $prevMonthTimestamp),
                'month'         => $month,
                'year'          => $year,
                'demandes'      => $demandes
            );
            $v = new CalendrierDemandeHistoryView();
            $v->show($params);
        }
    }
    
    public function doPeriodList() {
        // Si l'utilisateur n'est pas superviseur on interrompt le script
        if ($_SESSION['user_privileges'] != 'superviseur') {
            die();
        }
        $c = new ClasseModel();
        $classes = $c->listing();
        $m = new CalendrierModel();
        foreach ($classes as &$class) {
            $class['periods'] = $m->getPeriods($class['id']);
        }
        $v = new CalendrierShowPeriodesView();
        $v->show(array('classes' => $classes));
    }
    
    public function doPeriodDelete($args) {
        $period_id = $args['period_id'];
        // Si l'utilisateur n'est pas superviseur on interrompt le script
        if ($_SESSION['user_privileges'] != 'superviseur') {
            die();
        }
        
        $m = new CalendrierModel();
        $r = $m->getPeriod($period_id);
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['validation'])) {
                $m->deletePeriod($period_id);
            }
            Router::redirect('CalendarPeriodList', NULL, array('class_id' => $r['id_classe']));
        }
        $v = new CalendrierDeletePeriodView();
        $v->show($r);
    }
    
    public function doPeriodAdd() {
        // Si l'utilisateur n'est pas superviseur on interrompt le script
        if ($_SESSION['user_privileges'] != 'superviseur') {
            die();
        }
        
        $id = $this->_getArg('class_id');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['validation'])) {
                Router::redirect('CalendarPeriodList', NULL, array('class_id' => $id));
            }
            $date_debut = $this->UnixTimestampFromUsDateTime($this->FormatDateTimeFrToUs($this->_getArg('date_debut'), false));
            $date_fin = $this->UnixTimestampFromUsDateTime($this->FormatDateTimeFrToUs($this->_getArg('date_fin'), false));
            if (empty($date_debut)) {
                $_SESSION['ERROR_MSG'] = 'Veuillez saisir une date de début de période';
            } else if (empty($date_fin)) {
                $_SESSION['ERROR_MSG'] = 'Veuillez saisir une date de fin de période';
                
            } else {
                $parms = array(
                    'type'              => $this->_getArg('type'),
                    'date_debut'        => date('Y-m-d H:i:s', $date_debut),
                    'date_fin'          => date('Y-m-d H:i:s', $date_fin),
                    'id_classe'         => $id
                );
                $m = new CalendrierModel();
                $id_periode = $m->createPeriod($parms);
                if ($parms['type'] != 'vacances') {
                    foreach ($_POST as $k => $v) {
                        if (substr($k, 0, 5) == 'event') {
                            $tmp = explode('-', $v);
                            $this->dbo->insert('insert into modele_planning(jour, heure_debut, heure_fin, id_matiere, id_enseignant, id_classe, id_periode) values(' . intval($tmp[0] + 1) . ', "' . $this->FormatTimeFrToUs($tmp[1]) . '", "' . $this->FormatTimeFrToUs($tmp[2]) . '", ' . intval($tmp[3]) . ', (select id_enseignant from enseignants_matieres_classes where id_matiere=' . intval($tmp[3]) . ' and id_classe=' . $this->_getArg('class_id') . ' limit 0, 1), ' . $this->_getArg('class_id') . ', ' . $id_periode . ')');
                        }
                    }
                }
                Router::redirect('CalendarPeriodList', NULL, array('class_id' => $id));
            }
        }
        $m = new MatieresClasseModel();
        $r = array(
            'id_classe'     => $id,
            'matieres'      => $m->getSubjectsClass(array('id' => $id))
        );
        $v = new CalendrierAddPeriodView();
        $v->show($r);
    }
    
    public function doPeriodEdit($args) {
        $period_id = $args['period_id'];
        // Si l'utilisateur n'est pas superviseur on interrompt le script
        if ($_SESSION['user_privileges'] != 'superviseur') {
            die();
        }
        
        $m = new CalendrierModel();
        $r = $m->getPeriod($period_id);
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['validation'])) {
                $parms = array(
                    'type'              => $this->_getArg('type'),
                    'date_debut'        => $this->FormatDateTimeFrToUs($this->_getArg('date_debut'), false),
                    'date_fin'          => $this->FormatDateTimeFrToUs($this->_getArg('date_fin'), false),
                );
                $m = new CalendrierModel();
                $m->updatePeriod($period_id, $parms);
                if ($parms['type'] != 'vacances') {
                    $usedIds = array();
                    foreach ($_POST as $k => $v) {
                        if (substr($k, 0, 5) == 'event') {
                            $tmp = explode('-', $v);
                            // Si l'ID est composé de 5 composantes alors la 5ème est l'ID du cours et existe donc déjà (donc mise à jour et pas insertion afin de conserver la cohérence de sa clef primaire avec le reste de la base de données)
                            if (count($tmp) == 5) {
                                $this->dbo->insert('update modele_planning set
                                jour=' .  intval($tmp[0] + 1) . ',
                                heure_debut="' . $this->FormatTimeFrToUs($tmp[1]) . '",
                                heure_fin="' . $this->FormatTimeFrToUs($tmp[2]) . '",
                                id_matiere=' . intval($tmp[3]) . ',
                                id_enseignant=(select id_enseignant from enseignants_matieres_classes where id_matiere=' . intval($tmp[3]) . ' and id_classe=' .  $r['id_classe'] . ' limit 0, 1),
                                id_classe=' .  $r['id_classe'] . ',
                                id_periode=' . $id . '
                                where id=' . $tmp[4]);
                                
                                $usedIds[] = $tmp[4];
                            } else {
                                $usedIds[] = $this->dbo->insert('insert into modele_planning(jour, heure_debut, heure_fin, id_matiere, id_enseignant, id_classe, id_periode) values(' . intval($tmp[0] + 1) . ', "' . $this->FormatTimeFrToUs($tmp[1]) . '", "' . $this->FormatTimeFrToUs($tmp[2]) . '", ' . intval($tmp[3]) . ', (select id_enseignant from enseignants_matieres_classes where id_matiere=' . intval($tmp[3]) . ' and id_classe=' . $r['id_classe'] . ' limit 0, 1), ' . $r['id_classe'] . ', ' . $period_id . ')');
                            }
                        }
                    }
                    $this->dbo->delete('delete from modele_planning where id not in (' . implode(',', $usedIds) . ') and id_periode=' . $period_id);
                }
            }
            Router::redirect('CalendarPeriodList', array('id_classe' => $r['id_classe']));
        }
        $c = new ClasseModel();
        $v = new CalendrierEditPeriodView();
        $r['classes'] = $c->listing();
        $r['cours'] = $this->dbo->query('select id, jour+0 as jour, jour as jour_libelle, date_format(heure_debut, "%H:%i") as heure_debut, date_format(heure_fin, "%H:%i") as heure_fin, id_matiere, id_periode from modele_planning where id_classe=' . $r['id_classe'] . ' and id_periode=' . $period_id);
        $m = new MatieresClasseModel();
        $r['matieres'] = $m->getSubjectsClass($r['id_classe']);
        $v->show($r);
    }
}
?>
