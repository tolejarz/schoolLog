<?php
class MaterielModel extends Model {
    protected $_fields = array('id', 'type', 'modele', 'etat');
    
    public function __construct($values = array()) {
        $this->repository = new MaterielRepository();
        parent::__construct($values);
    }
}
?>
