<?php
class BackupHandler extends Handler {
    public function doAdd() {
        $backup_controller = new SauvegardeController($this->dbo);
        $backup_controller->doAdd($this->args);
    }
    
    public function doDelete() {
        $backup_controller = new SauvegardeController($this->dbo);
        $backup_controller->doDelete($this->args);
    }
    
    public function doList() {
        $backup_controller = new SauvegardeController($this->dbo);
        $backup_controller->doList();
    }
    
    public function doRestore() {
        $backup_controller = new SauvegardeController($this->dbo);
        $backup_controller->doRestore($this->args);
    }
}
?>
