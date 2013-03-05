<?php
class ResourceHandler extends Handler {
    public function doGet() {
        $resource_controller = new ResourceController($this->dbo);
        $resource_controller->doGet($this->args);
    }
}
?>
