<?php
class EnseignantsMatieresClassesModel extends Model {
    protected $_fields = array(
        'id_enseignant', 'id_matiere', 'id_classe',
        'nom_classe', 'nom_matiere'
    );
    
    public function __construct($values = array()) {
        $this->repository = new EnseignantsMatieresClassesRepository();
        parent::__construct($values);
    }
}
?>
