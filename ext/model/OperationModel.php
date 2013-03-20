<?php
class OperationModel extends Model {
    protected $_fields = array(
        'id', 'date_creation', 'date_origine', 'date_report', 'etat', 'id_enseignant', 'id_modele_planning',
        'nom_classe'
    );
    
    public function __construct($values = array()) {
        $this->repository = new OperationRepository();
        parent::__construct($values);
    }
}
?>
