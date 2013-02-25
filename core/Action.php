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
        $uri = new Uri();
        $query = $uri->buildQuery();
        if (!empty($query)) {
            /*
            $h = $query->getHandler();
            $handler = new $h['classname']($settings);
            $result = $handler->{$h['function']}($query);
            
            $formatter = new Formatter($query);
            $formatter->execute($result);
            */
        } else {
            die();
        }
    }
}
?>
