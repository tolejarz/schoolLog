<?php
class EquipmentHandler extends Handler {
    public function doAdd() {
        $equipment_controller = new EquipmentController($this->dbo);
        $equipment_controller->doAdd();
    }
    
    public function doEdit() {
        $equipment_controller = new EquipmentController($this->dbo);
        $equipment_controller->doEdit($this->args);
    }
    
    public function doDelete() {
        $equipment_controller = new EquipmentController($this->dbo);
        $equipment_controller->doDelete($this->args);
    }
    
    public function doList() {
        $equipment_controller = new EquipmentController($this->dbo);
        $equipment_controller->doList();
    }
    
}
?>
