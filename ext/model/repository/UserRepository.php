<?php
class UserRepository extends Repository {
    protected $table_name = 'utilisateurs';
    protected $fields = array(
        'id'                    => array('type' => '%i', 'nullable' => false, 'primary' => true),
        'login'                 => array('type' => '%s', 'nullable' => false),
        'civility'              => array('type' => '%s', 'nullable' => true),
        'nom'                   => array('type' => '%s', 'nullable' => false),
        'email'                 => array('type' => '%s', 'nullable' => false),
        'droits'                => array('type' => '%s', 'nullable' => false),
        'derniere_connexion'    => array('type' => '%s', 'nullable' => true),
        'charte_signee'         => array('type' => '%i', 'nullable' => false),
    );
    
    public function auth($login, $password) {
        //return $this->link->fetchOne('select * from user where login=%s and sha1(concat(username, password))=%s', $login, $password);
        return $this->link->fetchOne('select * from ' . $this->table_name . ' where login=%s', $login);
    }
    
    public function create($a) {
        $r = parent::create($a);
        if ($r !== false && !empty($a['customer_id'])) {
            $r2 = $this->link->insert('insert into user_customer (user_id, customer_id) values (%i, %i)', (int)$r['insert_id'], (int)$a['customer_id']);
        }
        return $r;
    }
}
?>
