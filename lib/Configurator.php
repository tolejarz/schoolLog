<?php
class Configurator {
    private static $instance;
    private $configuration;
    private $connections = array();
    
    private function __construct($filename) {
        $this->readConfiguration($filename);
    }
    
    public static function getInstance($filename = null) {
        if (!isset(self::$instance)) {
            self::$instance = new Configurator($filename);
        }
        return self::$instance;
    }
    
    public function readConfiguration($filename) {
        $c = file_get_contents($filename);
        $this->configuration = json_decode($c, true);
    }
    
    public function getConfiguration() {
        return $this->configuration;
    }
    
    public function getConnection($database) {
        if (!isset($this->connections[$database])) {
            $this->connections[$database] = new MySQLConnector(
                $this->configuration['database'][$database]['host'],
                $this->configuration['database'][$database]['username'],
                $this->configuration['database'][$database]['password'],
                $this->configuration['database'][$database]['database']
            );
        }
        return $this->connections[$database];
    }
}
?>
