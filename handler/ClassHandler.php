<?php
class ClassHandler extends Handler {
    public function doAdd() {
        $class_controller = new ClassController($this->dbo);
        $class_controller->doAdd();
    }
    
    public function doDelete() {
        $class_controller = new ClassController($this->dbo);
        $class_controller->doDelete($this->args);
    }
    
    public function doEdit() {
        $class_controller = new ClassController($this->dbo);
        $class_controller->doEdit($this->args);
    }
    
    public function doList() {
        $class_controller = new ClassController($this->dbo);
        $class_controller->doList();
    }
}
?>
