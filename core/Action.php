<?php
/**
 * A simple wrapper where we instanciate Query, Handler and Formatter.
 */
class Action {
    public function __construct() {}
    
    /**
     * Main method.
     * @param array $settings Application settings (to pass to Handler)
     */
    public function perform($settings) {

require_once('lib/epsilib.php');
require_once('config.php');

$configurator = Configurator::getInstance('config/config.json');
$conf = $configurator->getConfiguration();
$dbo = new MySQLiDBO($conf['database']['schoollog']['host'], $conf['database']['schoollog']['username'], $conf['database']['schoollog']['password'], $conf['database']['schoollog']['database']);

        
        
        
        
        
        
        $uri = new Uri();
        $query = $uri->buildQuery();
        if ($query['handler'] !== 'ResourceHandler') {
            $super = new SuperHandler(NULL, $conf, $dbo);
            $super->checkSession();
        }
        if (!empty($query)) {
            $handler = new $query['handler']($query['args'], $conf, $dbo);
            
            ob_start();
            $result = $handler->{$query['method']}();
            $html = ob_get_contents();
            ob_end_clean();
            
            /*
            $formatter = new Formatter($query);
            $formatter->execute($result);
            */
        } else {
            die();
        }
        
        
        
        
        if ($query['handler'] !== 'ResourceHandler') {
            include_once 'templates/layout/main.phtml';
        } else {
            echo $html;
        }
    }
}
?>
