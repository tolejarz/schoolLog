<?php
abstract class Handler {
    protected $settings;
    
    public function __construct($settings) {
        $this->settings = $settings;
    }
}
?>
