<?php
class ClassSubjectHandler extends Handler {
    public function doAdd() {
        $class_subject_controller = new MatieresClasseController($this->dbo);
        $class_subject_controller->doAdd($this->args);
    }
    
    public function doDelete() {
        $class_subject_controller = new MatieresClasseController($this->dbo);
        $class_subject_controller->doDelete($this->args);
    }
    
    public function doEdit() {
        $class_subject_controller = new MatieresClasseController($this->dbo);
        $class_subject_controller->doEdit($this->args);
    }
    
    public function doList() {
        $class_subject_controller = new MatieresClasseController($this->dbo);
        $class_subject_controller->doList();
    }
}
?>
