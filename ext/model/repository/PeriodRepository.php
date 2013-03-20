<?php
class PeriodRepository extends Repository {
    protected $table_name = 'periodes';
    protected $fields = array(
        'id'            => array('type' => '%i', 'nullable' => false, 'primary' => true),
        'type'          => array('type' => '%s', 'nullable' => false),
        'date_debut'    => array('type' => '%s', 'nullable' => true),
        'date_fin'      => array('type' => '%s', 'nullable' => true),
        'id_classe'     => array('type' => '%i', 'nullable' => false),
    );
    
    public function get($key, $value, $options) {
        $options['query'] = '
            select periodes.*, classes.libelle as nom_classe
            from periodes
            left join classes on classes.id=periodes.id_classe
        ';
        return parent::get($key, $value, $options);
    }
}
?>
