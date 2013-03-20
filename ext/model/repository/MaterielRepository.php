<?php
class MaterielRepository extends Repository {
    protected $table_name = 'materiels';
    protected $fields = array(
        'id'        => array('type' => '%i', 'nullable' => false, 'primary' => true),
        'type'      => array('type' => '%s', 'nullable' => false),
        'modele'    => array('type' => '%s', 'nullable' => false),
        'etat'      => array('type' => '%s', 'nullable' => false),
    );
}
?>
