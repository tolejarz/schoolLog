<?php
class SupportRepository extends Repository {
    protected $table_name = 'supports';
    protected $fields = array(
        'id'            => array('type' => '%i', 'nullable' => false, 'primary' => true),
        'date_creation' => array('type' => '%s', 'nullable' => false),
        'titre'         => array('type' => '%s', 'nullable' => false),
        'nom_fichier'   => array('type' => '%s', 'nullable' => false),
        'tags'          => array('type' => '%s', 'nullable' => false),
        'id_enseignant' => array('type' => '%i', 'nullable' => false),
        'id_matiere'    => array('type' => '%i', 'nullable' => false),
        'id_classe'     => array('type' => '%i', 'nullable' => false),
    );
    
    public function get($key, $value, $options) {
        $options['query'] = '
            select supports.*, classes.libelle as nom_classe, matieres.nom as nom_matiere
            from supports
            left join classes on classes.id=supports.id_classe
            left join matieres on matieres.id=supports.id_matiere
        ';
        return parent::get($key, $value, $options);
    }
    
    public function search($filters = array(), $options = array()) {
        $options['query'] = '
            select supports.*, classes.libelle as nom_classe, matieres.nom as nom_matiere
            from supports
            left join classes on classes.id=supports.id_classe
            left join matieres on matieres.id=supports.id_matiere
        ';
        return parent::search($filters, $options);
    }
}
?>
