<?php
class ReservationDefaultView extends HtmlView {
    public function show($data = array(), $materiels = array()) {
        $parms['booking'] = array(
            'dimanche'  => array(),
            'lundi'     => array(),
            'mardi'     => array(),
            'mercredi'  => array(),
            'jeudi'     => array(),
            'vendredi'  => array(),
            'samedi'    => array(),
        );
        $jours = array('dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi');
        foreach ($data['booking'] as $row) {
            $start_date = strtotime($row['date_heure_debut']);
            $start_day = date('w', $start_date);
            $start_hour = date('H:i:s', $start_date);
            
            $end_date = strtotime($row['date_heure_fin']);
            $end_hour = date('H:i:s', $end_date);
            
            $parms['booking'][$jours[$start_day]][$start_hour] = array(
                'id'                => $row['id'],
                'date_heure_debut'  => $row['date_heure_debut'],
                'heure_debut'       => $this->FormatDateTimeUsToFr($start_hour),
                'heure_fin'         => $this->FormatDateTimeUsToFr($end_hour),
                'etat'              => $row['etat'],
                'enseignant'        => $row['enseignant'],
                'id_enseignant'     => $row['id_enseignant'],
                'etat_materiel'     => $row['etat_materiel']
            );
        }
        $parms['id_materiel'] = $data['id_materiel'];
        $parms['_week'] = $data['_week'];
        $parms['materiels'] = $materiels;
        $this->_pushTemplate('templates/booking/list.phtml', $parms);
    }
}
?>
