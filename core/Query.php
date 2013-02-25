<?php
abstract class Query {
    protected $content_type = null;
    protected $handler_classname = null;
    protected $handler_func = null;
    protected $params;
    
    public function __construct() {
        
    }
    
    public function getHandler() {
        return array('classname' => $this->handler_classname, 'function' => $this->handler_func);
    }
    
    public function getContentType() {
        return $this->content_type;
    }
    
    public function setParams($params) {
        $this->params = $params;
    }
    
    public function getParams() {
        return $this->params;
    }
    
    public function getParam($p) {
        return isset($this->params[$p]) ? $this->params[$p] : null;
    }
    
    public function setParam($p, $v) {
        $this->params[$p] = $v;
    }
}
?>
