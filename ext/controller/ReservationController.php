<?php
// No SQL!!! :)
class ReservationController extends Controller {
    private function getFirstWeekDay($week) {
        return date('Y-m-d', strtotime(date('Y') . 'W' . str_pad($week, 2, '0', STR_PAD_LEFT) . '1')); // The last number is the num of the weekday. 0 being sunday.
    }
    
    private function getLastWeekDay($week) {
        return date('Y-m-d', strtotime(date('Y') . 'W' . str_pad($week, 2, '0', STR_PAD_LEFT) . '7')); // The last number is the num of the weekday. 0 being sunday.
    }
    
    public function doList() {
        if (in_array($_SESSION['user']['privileges'], array('superviseur', 'enseignant'))) {
            $week = $this->_getArg('week');
            $week = !empty($week) ? $week : (date('w') % 5 == 0 ? date('W') + 1: date('W'));
            
            $equipment = new MaterielModel();
            $equipments = $equipment->search();
            
            /* Si aucun matériel n'est passé en paramètres, on affiche les informations du premier matériel disponible dans la base */
            $id_materiel = $this->_getArg('id_materiel');
            if (empty($id_materiel) && !empty($equipments)) {
                $id_materiel = current($equipments)['id'];
            }
            
            $booking = new ReservationModel();
            $r['booking'] = $booking->search(array(
                'id_materiel'               => $id_materiel,
                'date_heure_debut between'  => array($this->getFirstWeekDay($week) . ' 00:00:00', $this->getLastWeekDay($week) . ' 23:59:59'))
            );
            $r['id_materiel'] = $id_materiel;
            $r['_week'] = $week;
            
            $v = new ReservationDefaultView();
            $v->show($r, $equipments);
        }
    }
    
    public function doDelete($args) {
        $booking_id = $args['booking_id'];
        if (in_array($_SESSION['user']['privileges'], array('superviseur', 'enseignant'))) {
            $booking = new ReservationModel();
            $booking->get($booking_id);
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['validation'])) {
                    if ($_SESSION['user']['privileges'] == 'superviseur') {
                        $v = new ReservationEmailView();
                        
                        $mm = new Mail();
                        $mm->SendMail($_SESSION['user']['email'], $booking->email_enseignant, 'materiel_delete', $v->show($booking->toArray()));
                    }
                    $booking->delete(array('id' => $booking_id));
                }
                Router::redirect('BookingList', NULL, array('id_materiel' => $this->_getArg('id_materiel')));
            }
            /* Récupération des informations associées à la réservation dans la base */
            $v = new ReservationDeleteView();
            $v->show($booking->toArray());
        }
    }
    
    /* Fonction pour ajouter une réservation */
    public function doAdd() {
        if (in_array($_SESSION['user']['privileges'], array('superviseur', 'enseignant'))) {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (!isset($_POST['validation'])) {
                    Router::redirect('BookingList', NULL, array('id_materiel' => $this->_getArg('id_materiel')));
                }
                
                if (!empty($_POST['date_reservation'])) {
                    $date_debut = $this->FormatDateTimeFrToUs($_POST['date_reservation'] . ' ' . sprintf('%02d:%02d', $_POST['heure_debut_h'], $_POST['heure_debut_m']));
                    $date_fin = $this->FormatDateTimeFrToUs($_POST['date_reservation'] . ' ' . sprintf('%02d:%02d', $_POST['heure_fin_h'], $_POST['heure_fin_m']));
                    $date_debut_timestamp = mktime(0, 0, 0, substr($_POST['date_reservation'], 3, 2), substr($_POST['date_reservation'], 0, 2), substr($_POST['date_reservation'], 6, 4));
                }
                
                $heure_debut = mktime(sprintf("%02d", $this->_getArg('heure_debut_h')), sprintf("%02d", $this->_getArg('heure_debut_m')), 0, 0, 0, 0);
                $heure_fin = mktime(sprintf("%02d", $this->_getArg('heure_fin_h')), sprintf("%02d", $this->_getArg('heure_fin_m')), 0, 0, 0, 0);
                
                /* Gestion des erreurs */
                if (empty($_POST['date_reservation'])) {
                    $_SESSION['ERROR_MSG'] = 'Veuillez choisir une date pour votre réservation';
                } else if (date('w', $date_debut_timestamp) % 6 == 0) {
                    $_SESSION['ERROR_MSG'] = 'Veuillez choisir un jour différent de samedi ou dimanche pour la date de réservation';
                } else if ($heure_fin < $heure_debut) {
                    $_SESSION['ERROR_MSG'] = 'Veuillez choisir une heure de début de réservation antérieure à l\'heure de fin';
                } else if (empty($_POST['id_enseignant'])) {
                    $_SESSION['ERROR_MSG'] = 'Veuillez choisir un enseignant';
                } else if (empty($_POST['id_materiel'])) {
                    $_SESSION['ERROR_MSG'] = 'Veuillez choisir un matériel';
                } else if ($this->dbo->sqleval('select count(*) from reservations where id_materiel=' . $_POST['id_materiel'] . ' and id_enseignant=' . $_POST['id_enseignant'] . ' and date_heure_debut="' . $date_debut . '" and date_heure_fin="' . $date_fin . '"') > 0) {
                    $_SESSION['ERROR_MSG'] = 'Cette réservation existe déjà';
                } else if ($this->dbo->sqleval('select count(*) from reservations where id_materiel=' . $_POST['id_materiel'] . ' and (date_heure_debut between "' . $date_debut . '" and "' . $date_fin . '" or date_heure_fin between "' . $date_debut . '" and "' . $date_fin . '")') > 0) {
                    $type_choisi = $this->dbo->sqleval('select type from materiels where id=' . $_POST['id_materiel']);
                    $autre_prop = $this->dbo->singleQuery('select modele from materiels where type="' . $type_choisi . '" and id!=' . $_POST['id_materiel'] . ' and id not in (select id_materiel from reservations where date_heure_debut between "' . $date_debut . '" and "' . $date_fin . '" or date_heure_fin between "' . $date_debut . '" and "' . $date_fin . '")');
                    if (!empty($autre_prop)) {
                        $autre_proposition = '<br />Autre proposition : <b>' . $type_choisi . ' ' . $autre_prop['modele'] . '</b>';
                    } else {
                        $autre_proposition = '<br />Désolé, aucun matériel de type <b>' . $type_choisi . '</b> n\'est disponible le <b>' . $_POST['date_reservation'] . '</b> entre <b>' . sprintf('%02d:%02d', $_POST['heure_debut_h'], $_POST['heure_debut_m']) . '</b> et <b>' . sprintf('%02d:%02d', $_POST['heure_fin_h'], $_POST['heure_fin_m']) . '</b>';
                    }
                    $_SESSION['ERROR_MSG'] = 'Ce matériel est déjà réservé pour le créneau choisi' . $autre_proposition;
                } else {
                    $parms = array(
                        'date_creation'         => date('Y-m-d H:i:s'),
                        'date_heure_debut'      => $date_debut,
                        'date_heure_fin'        => $date_fin,
                        'id_enseignant'         => $_POST['id_enseignant'],
                        'id_materiel'           => $_POST['id_materiel'],
                        'etat'                  => ($_SESSION['user']['privileges'] == 'superviseur' ? 'validée' : '')
                    );
                    $booking = new ReservationModel($parms);
                    $booking->save();
                    $id = $booking->id;
                    if (!empty($id) && $_SESSION['user']['privileges'] == 'enseignant') {
                        /* Si un enseignant fait la demande, envoi du mail au superviseur pour confirmation */
                        $booking->get($id);
                        $parms = array(
                            'date_heure_debut'  => $booking->date_heure_debut,
                            'date_heure_fin'    => $booking->date_heure_fin,
                            'enseignant'        => $booking->enseignant,
                            'type'              => $booking->type,
                            'modele'            => $booking->modele,
                        );
                        
                        $user = new UserModel();
                        $users = $user->search(array('droits find' => 'superviseur'));
                        $emails = array();
                        foreach ($users as $superviseur) {
                            $emails[] = $superviseur['email'];
                        }
                        $emails = implode(';', $emails);
                        
                        $v = new ReservationEmailView();
                        
                        $m = new Mail();
                        $m->SendMail($_SESSION['user']['email'], $emails, 'materiel_new_prof', $v->show($parms));
                    } elseif (!empty($id) && $_SESSION['user']['privileges'] == 'superviseur') {
                        /* Si le superviseur fait la demande, envoi du mail a l'enseignant concerné pour confirmation */
                        $booking->get($id);
                        
                        $parms = array(
                            'date_heure_debut'  => $booking->date_heure_debut,
                            'date_heure_fin'    => $booking->date_heure_fin,
                            'enseignant'        => $booking->enseignant,
                            'type'              => $booking->type,
                            'modele'            => $booking->modele,
                        );
                        $v = new ReservationEmailView();
                        
                        $m = new Mail();
                        $m->SendMail($_SESSION['user']['email'], $infos['email'], 'materiel_new_superviseur', $v->show($parms));
                    }
                    Router::redirect('BookingList', NULL, array('id_materiel' => $this->_getArg('id_materiel')));
                }
            }
            
            /* Récupération de la liste du matériel dans la base */
            $equipment = new MaterielModel();
            $equipments = $equipment->search(array('etat' => 'fonctionnel'));
            
            /* Récupération de la liste des enseignants dans la base */
            $user = new UserModel();
            $enseignants = $user->search(array('droits find' => 'enseignant'));
            
            $params = array(
                'materiels'         => $equipments,
                'enseignants'       => $enseignants,
                'id_materiel'       => $this->_getArg('id_materiel')
            );
            $v = new ReservationAddView();
            $v->show($params);
        }
    }
    
    /* Fonction pour refuser une réservation */
    public function doReject() {
        if ($_SESSION['user']['privileges'] == 'superviseur') {
            $booking = new ReservationModel();
            $booking->get($this->_getArg('id'));
            
            $v = new ReservationEmailView();
            
            $m = new Mail();
            $m->SendMail($_SESSION['user']['email'], $infos['email'], 'materiel_reject', $v->show($booking->toArray()));
            
            $m = new ReservationModel();
            $m->delete(array('id' => $this->_getArg('id')));
            die();
        }
    }
    
    /* Fonction pour accepter une réservation */
    public function doAccept() {
        if ($_SESSION['user']['privileges'] == 'superviseur') {
            /* envoi du mail à l'enseignant concerné */
            $booking = new ReservationModel();
            $booking->get($this->_getArg('id'));
            $v = new ReservationEmailView();
            
            $m = new Mail();
            $m->SendMail($_SESSION['user']['email'], $infos['email'], 'materiel_accept', $v->show($booking->toArray()));
            /* Changer l'etat de la réservation dans la base */
            $m = new ReservationModel();
            $m->update($this->_getArg('id'), array('etat' => 'validée'));
            die();
        }
    }
}
?>
