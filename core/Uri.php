<?php
class Uri {
    private $uri;
    private $arguments;
    
    public function __construct() {
        $files = glob('routes/*.json');
        $this->uri = array();
        if (!empty($files)) {
            foreach ($files as $file) {
                $c = file_get_contents($file);
                $this->uri += json_decode($c, true);
            }
        }
    }
    
    private function handleRawRequest(&$query) {
        $url = $this->getFullUrl();
        $method = $_SERVER['REQUEST_METHOD'];
        switch ($method) {
            case 'GET':
            case 'HEAD':
                $arguments = $_GET;
                break;
            case 'POST':
                $arguments = $_POST;
               break;
            case 'PUT':
            case 'DELETE':
                parse_str(file_get_contents('php://input'), $arguments);
                break;
        }
        $query->setParams($arguments);
        if (isset($_SERVER['HTTP_ACCEPT'])) {
            $accept = $_SERVER['HTTP_ACCEPT'];
        } else {
            $accept = null;
        }
        $this->handleRequest($url, $method, $arguments, $accept);
    }
    
    private function getFullUrl() {
        $protocol = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
        $location = $_SERVER['REQUEST_URI'];
        
        if ($_SERVER['QUERY_STRING']) {
            $location = substr($location, 0, strrpos($location, $_SERVER['QUERY_STRING']) - 1);
        }
        return $protocol.'://'.$_SERVER['HTTP_HOST'].$location;
    }
    
    private function handleRequest($url, $method, $arguments, $accept) {
        switch ($method) {
            case 'GET':
                $this->performGet($url, $arguments, $accept);
                break;
            case 'HEAD':
                $this->performHead($url, $arguments, $accept);
                break;
            case 'POST':
                $this->performPost($url, $arguments, $accept);
                break;
            case 'PUT':
                $this->performPut($url, $arguments, $accept);
                break;
            case 'DELETE':
                $this->performDelete($url, $arguments, $accept);
                break;
            default:
                /* 501 (Not Implemented) for any unknown methods */
                header('Methode not Allowed', true, 501);
        }
    }
    
    protected function methodNotAllowedResponse() {
        /* 405 (Method Not Allowed) */
        header('Methode not Allowed', true, 405);
    }
    
    private function performGet($url, $arguments, $accept) {
        $this->arguments = $arguments;
    }
    
    private function performHead($url, $arguments, $accept) {
        
    }
    
    private function performPost($url, $arguments, $accept) {
        
    }
    
    private function performPut($url, $arguments, $accept) {
        
    }
    
    private function performDelete($url, $arguments, $accept) {
        
    }
    
    public function buildQuery() {
        $query = null;
        //$url_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $url_path = explode('?', $_SERVER['REQUEST_URI']);
        $url_path = rtrim($url_path[0], '/');
        $url_matched = false;
        
        $request_method = !empty($_SERVER['REQUEST_METHOD']) ? strtolower(trim($_SERVER['REQUEST_METHOD'])) : NULL;
        
        
        $r = NULL;
        foreach ($this->uri as $action => $query_params) {
            if (preg_match('`^' . rtrim($query_params['uri'], '/') . '$`', $url_path, $matches)) {
                $url_matched = true;
                $handler_class = $handler_method = '';
                if (!empty($query_params['handler'])) {
                    $handler = explode('.', $query_params['handler']);
                    $handler_class = $handler[0];
                    $handler_method = 'do' . ucfirst($handler[1]);
                }
                
                $args = array();
                array_shift($matches);
                foreach ($query_params['args'] as $i => $arg) {
                    $args[$arg] = isset($matches[$i]) ? urldecode(urldecode($matches[$i])) : NULL;
                }
                
                $r = array(
                    'handler'   => $handler_class,
                    'method'    => $handler_method,
                    'args'      => $args,
                );
                
                /*
                $query_params['method'] = !empty($query_params['method']) ? strtolower(trim($query_params['method'])) : NULL;
                if (!empty($request_method) &&
                    !empty($query_params['method']) &&
                    ($request_method !== $query_params['method'])) {
                    error_log(sprintf('(non rejected) %s method does not matched with URI %s (referer: %s)', $request_method, $url_path, !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'unknown'));
                }
                $query = new $query_params['class']($args);
                $this->handleRawRequest($query);
                */
                break;
            }
        }
        if (!$url_matched) {
            error_log(sprintf('%s URI not matched (referer: %s)', $url_path, !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'unknown'));
        }
        
        return !empty($r) ? $r : NULL;
        //return $query;
    }
}
?>
