<?php
abstract class Handler {
    protected $args;
    protected $settings;
    protected $dbo;
    
    public function __construct($args, $settings, $dbo) {
        $this->args = $args;
        $this->settings = $settings;
        $this->dbo = $dbo;
    }
}
?>
