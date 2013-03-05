<?php
class Router {
    private static $instance;
    private static $routes = NULL;
    
    private function __construct() {
    }
    
    public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new Router();
        }
        return self::$instance;
    }
    
    public static function build($route, $args = array(), $query_parms = array()) {
        if (self::$routes === NULL) {
            $uri = file_get_contents('routes/uri.json');
            self::$routes = json_decode($uri, true);
        }
        
        //if (!array_key_exists($route, $uri)) die();
        
        if (!empty($args)) {
            $new_uri = vsprintf(self::$routes[$route]['reverse_uri'], array_values($args));
        } else {
            $new_uri = self::$routes[$route]['uri'];
        }
        
        if (!empty($query_parms)) {
            $new_uri .= '?' . http_build_query($query_parms);
        }
        //error_log('Building route: ' . $route . ' (generated URI: ' . $new_uri . ')');
        return $new_uri;
    }
    
    public static function redirect($route, $args = array(), $query_parms = array()) {
        if (self::$routes === NULL) {
            $uri = file_get_contents('routes/uri.json');
            self::$routes = json_decode($uri, true);
        }
        
        if (!array_key_exists($route, self::$routes)) die();
        
        if (!empty($args)) {
            $new_uri = vsprintf(self::$routes[$route]['reverse_uri'], array_values($args));
        } else {
            $new_uri = self::$routes[$route]['uri'];
        }
        
        if (!empty($query_parms)) {
            $new_uri .= '?' . http_build_query($query_parms);
        }
        
        //error_log('Redirect to route: ' . $route . ' (generated URI: ' . $new_uri . ')');
        
        header('Location: /' . ltrim($new_uri, '/'));
        die();
    }
}
?>
