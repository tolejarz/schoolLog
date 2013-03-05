<?php
class AuthHandler extends Handler {
    public function doLogout() {
        $auth_controller = new AuthController($this->dbo);
        $auth_controller->doLogout();
    }
}
?>
