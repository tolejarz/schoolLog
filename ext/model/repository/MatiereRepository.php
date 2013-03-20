<?php
class MatiereRepository extends Repository {
    protected $table_name = 'matieres';
    protected $fields = array(
        'id'    => array('type' => '%i', 'nullable' => false, 'primary' => true),
        'nom'   => array('type' => '%s', 'nullable' => false),
    );
}
?>
