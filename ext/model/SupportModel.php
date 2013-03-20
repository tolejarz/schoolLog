<?php
class SupportModel extends Model {
    protected $_fields = array(
        'id', 'date_creation', 'titre', 'nom_fichier', 'tags', 'id_enseignant', 'id_matiere', 'id_classe',
        'nom_classe', 'nom_matiere'
    );
    
    public function __construct($values = array()) {
        $this->repository = new SupportRepository();
        parent::__construct($values);
    }
}
?>
