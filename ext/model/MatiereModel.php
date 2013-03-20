<?php
class MatiereModel extends Model {
    protected $_fields = array('id', 'nom');
    
    public function __construct($values = array()) {
        $this->repository = new MatiereRepository();
        parent::__construct($values);
    }
}
?>
