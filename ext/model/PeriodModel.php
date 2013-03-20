<?php
class PeriodModel extends Model {
    protected $_fields = array(
        'id', 'type', 'date_debut', 'date_fin', 'id_classe',
        'nom_classe'
    );
    
    public function __construct($values = array()) {
        $this->repository = new PeriodRepository();
        parent::__construct($values);
    }
}
?>
