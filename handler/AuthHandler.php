<?php
class AuthHandler extends Handler {
    public function doLogout() {
        $auth_controller = new AuthController($this->dbo);
        $auth_controller->doLogout();
    }
    
    public function doLogin() {
        $auth_controller = new AuthController($this->dbo);
        $auth_controller->doLogin();
    }
    
    public function doCharte() {
        $auth_controller = new AuthController($this->dbo);
        $auth_controller->doCharte();
    }
}
?>
