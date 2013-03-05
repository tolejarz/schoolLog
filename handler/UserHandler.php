<?php
class UserHandler extends Handler {
    public function doAdd() {
        $user_controller = new UserController($this->dbo);
        $user_controller->doAdd();
    }
    
    public function doDelete() {
        $user_controller = new UserController($this->dbo);
        $user_controller->doDelete($this->args);
    }
    
    public function doEdit() {
        $user_controller = new UserController($this->dbo);
        $user_controller->doEdit($this->args);
    }
    
    public function doList() {
        $user_controller = new UserController($this->dbo);
        $user_controller->doList();
    }
}
?>
