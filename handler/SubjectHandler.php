<?php
class SubjectHandler extends Handler {
    public function doAdd() {
        $subject_controller = new MatiereController($this->dbo);
        $subject_controller->doAdd();
    }
    
    public function doDelete() {
        $subject_controller = new MatiereController($this->dbo);
        $subject_controller->doDelete($this->args);
    }
    
    public function doEdit() {
        $subject_controller = new MatiereController($this->dbo);
        $subject_controller->doEdit($this->args);
    }
    
    public function doList() {
        $subject_controller = new MatiereController($this->dbo);
        $subject_controller->doList();
    }
}
?>
