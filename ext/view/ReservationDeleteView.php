<?php
class ReservationDeleteView extends HtmlView {
    function show($viewparms = array()) {
        $parms = array(
            'id'            => $viewparms['id'],
            'date_debut'    => $this->FormatDateTimeUsToFr($viewparms['date_heure_debut'], false),
            'heure_debut'   => $this->FormatTimeUsToFr($viewparms['date_heure_debut']),
            'heure_fin'     => $this->FormatTimeUsToFr($viewparms['date_heure_fin']),
            'enseignant'    => $viewparms['enseignant'],
            'materiel'      => $viewparms['materiel'],
            'id_materiel'   => $viewparms['id_materiel']
        );
        $this->_pushTemplate('templates/booking/delete.phtml', $parms);
    }
}
?>
