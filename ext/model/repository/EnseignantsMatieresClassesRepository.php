<?php
class EnseignantsMatieresClassesRepository extends Repository {
    protected $table_name = 'enseignants_matieres_classes';
    protected $fields = array(
        'id_enseignant' => array('type' => '%i', 'nullable' => false),
        'id_matiere'    => array('type' => '%i', 'nullable' => false),
        'id_classe'     => array('type' => '%i', 'nullable' => false),
    );
    
    public function get($key, $value, $options) {
        $options['query'] = '
            select enseignants_matieres_classes.*, matieres.nom as nom_matiere, classes.libelle as nom_classe
            from enseignants_matieres_classes
            left join matieres on matieres.id=enseignants_matieres_classes.id_matiere
            left join classes on classes.id=enseignants_matieres_classes.id_classe
        ';
        return parent::get($key, $value, $options);
    }
    
    public function search($filters = array(), $options = array()) {
        $options['query'] = 'select
            matieres.id as id_matiere,
            matieres.nom as nom_matiere,
            
            classes.id as id_classe,
            classes.libelle as nom_classe,
            
            utilisateurs.id as id_enseignant,
            utilisateurs.nom as nom_enseignant
            
            from ' . $this->table_name . '
            left join matieres on matieres.id=' . $this->table_name . '.id_matiere
            left join classes on classes.id=' . $this->table_name . '.id_classe
            left join utilisateurs on utilisateurs.id=' . $this->table_name . '.id_enseignant
        ';
        return parent::search($filters, $options);
    }
}
?>
