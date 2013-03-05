<?php
class ReservationController extends Controller {
	
	/* Fonction pour afficher la liste des réservations d'une semaine*/
	function doList() {
		if ($_SESSION['user_privileges'] == 'superviseur' || $_SESSION['user_privileges'] == 'enseignant') {
			$week = $this->_getArg('week');
			$week = !empty($week) ? $week : (date('w') % 5 == 0 ? date('W') + 1: date('W'));
			$arg = '&amp;id_materiel=' . $this->_getArg('id_materiel');
			
			/* Récupération de la liste du materiel dans la base */
			$m = new MaterielModel($this->dbo);
			$materiels = $m->listing();
			
			/* Si aucun matériel n'est passé en paramètres, on affiche les informations du premier matériel disponible dans la base */
			$id_materiel = $this->_getArg('id_materiel');
			if (empty($id_materiel)) {
				if (!empty($materiels)) {
					$id_materiel = $materiels[0]['id'];
				}
			}
			
			/* Récupération des informations relatives aux réservations de la semaine $week dans la base */
			$m = new ReservationModel($this->dbo);
			$r = $m->get(array('id_materiel' => $id_materiel, 'week' => $week));
			$r['id_materiel'] = $id_materiel;
			$r['_arg'] = $arg;
			$r['_week'] = $week;
			
			$v = new ReservationDefaultView();
			$v->show($r, $materiels);
		}
	}
	
	/* Fonction pour supprimer une réservation */
	function _doDelete() {
		if ($_SESSION['user_privileges'] == 'superviseur' || $_SESSION['user_privileges'] == 'enseignant') {
			$m = new ReservationModel($this->dbo);
			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				if (isset($_POST['validation'])) {
					if ($_SESSION['user_privileges'] == 'superviseur') {
						/* envoi du mail à l'enseignant concerné */
						$infos = $this->dbo->singleQuery('select date_heure_debut, date_heure_fin, m.type as type, m.modele as modele, u.email as email, concat(u.civility, " ", u.nom) as enseignant from materiels m, reservations r, utilisateurs u where r.id=' . $this->_getArg('id') . ' and r.id_materiel=m.id and r.id_enseignant=u.id');
						$parms = array('date_heure_debut' => $infos['date_heure_debut'], 'date_heure_fin' => $infos['date_heure_fin'], 'enseignant' =>$infos['enseignant'], 'type'=> $infos['type'], 'modele'=> $infos['modele']);
						
						$v = new ReservationEmailView();
						
						$mm = new Mail();
						$mm->SendMail($_SESSION['user_email'], $infos['email'], 'materiel_delete', $v->show($parms));
					}
					/* Suppression de la réservation dans la base */
					$m->delete($this->_getArg('id'));
				}
				$this->redirect('Booking', array('id_materiel' => $this->_getArg('id_materiel')));
			}
			/* Récupération des informations associées à la réservation dans la base */
			$r = $m->getReservation(array('id' => $this->_getArg('id')));
			$params = array(
				'id' 					=> $this->_getArg('id'),
				'date_reservation'		=> $r['date_reservation'],
				'heure_debut'			=> $r['heure_debut'],
				'heure_fin' 			=> $r['heure_fin'],
				'enseignant' 			=> $r['enseignant'],
				'materiel' 				=> $r['materiel'],
				'id_materiel' 			=> $this->_getArg('id_materiel')
			);
			$v = new ReservationDeleteView();
			$v->show($params);
		}
	}
	
	/* Fonction pour ajouter une réservation */
	function _doAdd() {
		if (in_array($_SESSION['user_privileges'], array('superviseur', 'enseignant'))) {
			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				if (!isset($_POST['validation'])) {
					$this->redirect('Booking', array('id_materiel' => $this->_getArg('id_materiel')));
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
						'date_heure_debut' 		=> $date_debut,
						'date_heure_fin' 		=> $date_fin,
						'id_enseignant' 		=> $_POST['id_enseignant'],
						'id_materiel' 			=> $_POST['id_materiel'],
						'etat'					=> ($_SESSION['user_privileges'] == 'superviseur' ? 'validée' : '')
					);
					$s = new ReservationModel($this->dbo);
					$id = $s->create($parms);
					if (!empty($id) && $_SESSION['user_privileges'] == 'enseignant') {
						/* Si un enseignant fait la demande, envoi du mail au superviseur pour confirmation */
						$infos = $this->dbo->singleQuery('select m.type as type, m.modele as modele, concat(u.civility, " ", u.nom) as enseignant from materiels m, reservations r, utilisateurs u where m.id=' . $parms['id_materiel'] . ' and r.id_materiel=m.id and r.id_enseignant=u.id');
						
						$parms = array('date_heure_debut' => $parms['date_heure_debut'], 'date_heure_fin' => $parms['date_heure_fin'], 'enseignant' =>$infos['enseignant'], 'type'=> $infos['type'], 'modele'=> $infos['modele']);
						
						$ress = $this->dbo->query('select email from utilisateurs where find_in_set("superviseur", droits) > 0');
						$emails = array();
						foreach ($ress as $superviseur) {
							$emails[] = $superviseur['email'];
						}
						$emails = implode(';', $emails);
						
						$v = new ReservationEmailView();
						
						$m = new Mail();
						$m->SendMail($_SESSION['user_email'], $emails, 'materiel_new_prof', $v->show($parms));
					} else if (!empty($id) && $_SESSION['user_privileges'] == 'superviseur') {
						/* Si le superviseur fait la demande, envoi du mail a l'enseignant concerné pour confirmation */
						$infos = $this->dbo->singleQuery('select m.type as type, m.modele as modele, u.email as email, concat(u.civility, " ", u.nom) as enseignant from materiels m, reservations r, utilisateurs u where m.id=' . $parms['id_materiel'] . ' and r.id_materiel=m.id and r.id_enseignant=u.id');
						
						$parms = array('date_heure_debut' => $parms['date_heure_debut'], 'date_heure_fin' => $parms['date_heure_fin'], 'enseignant' =>$infos['enseignant'], 'type'=> $infos['type'], 'modele'=> $infos['modele']);
						$v = new ReservationEmailView();
						
						$m = new Mail();
						$m->SendMail($_SESSION['user_email'], $infos['email'], 'materiel_new_superviseur', $v->show($parms));
					}
					$this->redirect('Booking', array('id_materiel' => $this->_getArg('id_materiel')));
				}
			}
			
			/* Récupération de la liste du matériel dans la base */
			$m = new MaterielModel($this->dbo);
			$materiels = $m->listingFonctionnels();
			
			/* Récupération de la liste des enseignants dans la base */
			$m = new UserModel($this->dbo);
			$enseignants = $m->listingEnseignants();
			
			$params = array(
				'materiels' 		=> $materiels,
				'enseignants' 		=> $enseignants,
				'id_materiel' 		=> $this->_getArg('id_materiel')
				);
			$v = new ReservationAddView();
			$v->show($params);
		}
	}
	
	/* Fonction pour refuser une réservation */
	function _doReject() {
		if ($_SESSION['user_privileges'] == 'superviseur') {
			
			/* envoi du mail à l'enseignant concerné */
			$infos = $this->dbo->singleQuery('select date_heure_debut, date_heure_fin, m.type as type, m.modele as modele, u.email as email, concat(u.civility, " ", u.nom) as enseignant from materiels m, reservations r, utilisateurs u where r.id=' . $this->_getArg('id') . ' and r.id_materiel=m.id and r.id_enseignant=u.id');
			$parms = array('date_heure_debut' => $infos['date_heure_debut'], 'date_heure_fin' => $infos['date_heure_fin'], 'enseignant' =>$infos['enseignant'], 'type'=> $infos['type'], 'modele'=> $infos['modele']);
			
			$v = new ReservationEmailView();
			
			$m = new Mail();
			$m->SendMail($_SESSION['user_email'], $infos['email'], 'materiel_reject', $v->show($parms));
			/* Suppression de la réservation dans la base */
			$m = new ReservationModel($this->dbo);
			$m->delete($this->_getArg('id'));
			die();
		}
	}
	
	/* Fonction pour accepter une réservation */
	function _doAccept() {
		if ($_SESSION['user_privileges'] == 'superviseur') {
			/* envoi du mail à l'enseignant concerné */
			$infos = $this->dbo->singleQuery('select date_heure_debut, date_heure_fin, m.type as type, m.modele as modele, u.email as email, concat(u.civility, " ", u.nom) as enseignant from materiels m, reservations r, utilisateurs u where r.id=' . $this->_getArg('id') . ' and r.id_materiel=m.id and r.id_enseignant=u.id');
			$parms = array('date_heure_debut' => $infos['date_heure_debut'], 'date_heure_fin' => $infos['date_heure_fin'], 'enseignant' =>$infos['enseignant'], 'type'=> $infos['type'], 'modele'=> $infos['modele']);
			
			$v = new ReservationEmailView();
			
			$m = new Mail();
			$m->SendMail($_SESSION['user_email'], $infos['email'], 'materiel_accept', $v->show($parms));
			/* Changer l'etat de la réservation dans la base */
			$m = new ReservationModel($this->dbo);
			$m->update($this->_getArg('id'), array('etat' => 'validée'));
			die();
		}
	}
}
?>
