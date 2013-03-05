<?php
class CalendarHandler extends Handler {
    public function doShow() {
        $calendar_controller = new CalendrierController($this->dbo);
        $calendar_controller->doShow();
    }
    
    public function doTeacher() {
        $calendar_controller = new CalendrierController($this->dbo);
        $calendar_controller->doTeacher();
    }
    
    public function doClass() {
        $calendar_controller = new CalendrierController($this->dbo);
        $calendar_controller->doClass();
    }
    
    public function doRequestList() {
        $calendar_controller = new CalendrierController($this->dbo);
        $calendar_controller->doRequestList();
    }
    
    public function doRequestAdd() {
        $calendar_controller = new CalendrierController($this->dbo);
        $calendar_controller->doRequestAdd();
    }
    
    public function doRequestAccept() {
        $calendar_controller = new CalendrierController($this->dbo);
        $calendar_controller->doRequestAccept($this->args);
    }
    
    public function doRequestReject() {
        $calendar_controller = new CalendrierController($this->dbo);
        $calendar_controller->doRequestReject($this->args);
    }
    
    public function doRequestHistory() {
        $calendar_controller = new CalendrierController($this->dbo);
        $calendar_controller->doRequestHistory();
    }
    
    public function doPeriodList() {
        $calendar_controller = new CalendrierController($this->dbo);
        $calendar_controller->doPeriodList();
    }
    
    public function doPeriodAdd() {
        $calendar_controller = new CalendrierController($this->dbo);
        $calendar_controller->doPeriodAdd();
    }
    
    public function doPeriodDelete() {
        $calendar_controller = new CalendrierController($this->dbo);
        $calendar_controller->doPeriodDelete($this->args);
    }
    
    public function doPeriodEdit() {
        $calendar_controller = new CalendrierController($this->dbo);
        $calendar_controller->doPeriodEdit($this->args);
    }
}
?>
