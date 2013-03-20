<?php
class UserModel extends Model {
    protected $_fields = array('id', 'login', 'civility', 'nom', 'email', 'droits', 'derniere_connexion', 'charte_signee');
    
    public function __construct($values = array()) {
        $this->repository = new UserRepository();
        parent::__construct($values);
    }
    
    public function auth($login, $password) {
        $r = $this->repository->auth($login, $password);
        $this->_loaded = !empty($r);
        $this->fromArray($r);
        return $this->_loaded;
    }
}
?>
