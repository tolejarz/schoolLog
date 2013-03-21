<?php
class ReservationModel extends Model {
    protected $_fields = array(
        'id', 'date_creation', 'date_heure_debut', 'date_heure_fin', 'id_enseignant', 'id_materiel', 'etat'
    );
    
    public function __construct($values = array()) {
        $this->repository = new ReservationRepository();
        parent::__construct($values);
    }
}
?>
