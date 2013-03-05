<?php
class SupportHandler extends Handler {
    public function doList() {
        $support_controller = new SupportController($this->dbo);
        $support_controller->doList();
    }
    
    public function doAdd() {
        $support_controller = new SupportController($this->dbo);
        $support_controller->doAdd();
    }
    
    public function doDelete() {
        $support_controller = new SupportController($this->dbo);
        $support_controller->doDelete($this->args);
    }
    
    public function doEdit() {
        $support_controller = new SupportController($this->dbo);
        $support_controller->doEdit($this->args);
    }
}
?>
