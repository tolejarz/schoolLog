<?php
class ClasseRepository extends Repository {
    protected $table_name = 'classes';
    protected $fields = array(
        'id'        => array('type' => '%i', 'nullable' => false, 'primary' => true),
        'libelle'   => array('type' => '%s', 'nullable' => false),
        'email'     => array('type' => '%s', 'nullable' => false),
    );
}
?>
