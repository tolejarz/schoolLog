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
    public function perform($configuration) {
        require_once('config.php');
        
        $dbo = new MySQLiDBO(
            $configuration['database']['schoollog']['host'],
            $configuration['database']['schoollog']['username'],
            $configuration['database']['schoollog']['password'],
            $configuration['database']['schoollog']['database']
        );
        
        $uri = new Uri();
        $query = $uri->buildQuery();
        if ($query['handler'] !== 'ResourceHandler') {
            $super = new SuperHandler(NULL, $configuration, $dbo);
            $super->checkSession();
        }
        if (!empty($query)) {
            $handler = new $query['handler']($query['args'], $configuration, $dbo);
            
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
        include_once 'templates/layout/main.phtml';
    }
}
?>
