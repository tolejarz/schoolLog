<?php
class ClasseModel extends Model {
    protected $_fields = array('id', 'libelle', 'email');
    
    public function __construct($values = array()) {
        $this->repository = new ClasseRepository();
        parent::__construct($values);
    }
}
?>
