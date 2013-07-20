<?php
class ModelePlanningModel extends Model {
    protected $_fields = array('id', 'jour', 'heure_debut', 'heure_fin', 'id_matiere', 'id_enseignant', 'id_classe', 'id_periode');
    
    public function __construct($values = array()) {
        $this->repository = new ModelePlanningRepository();
        parent::__construct($values);
    }
}
?>
