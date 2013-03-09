<?php
abstract class Model {
    protected $repository;
    protected $attributes = array();
    protected $_loaded;
    public $_found_rows;
    
    public function __construct($values) {
        if (is_array($values) && !empty($values)) {
            foreach ($values as $field_name => $field_value) {
                $this->$field_name = $field_value;
            }
        }
    }
    
    public function get($id, $key = NULL, $options = array()) {
        $r = $this->repository->get($key, $id, $options);
        $this->_loaded = !empty($r);
        $this->fromArray($r);
        return $this->_loaded;
    }
    
    public function save() {
        $fields = $this->toArray();
        if ($this->_loaded) {
            $r = $this->repository->update($fields);
            return $r['affected_rows'] >= 1;
        } else {
            $r = $this->repository->create($fields);
            if ($r['affected_rows'] >= 1) {
                if (in_array('id', $this->_fields)) {
                    $this->attributes['id'] = $r['insert_id'];
                }
                return true;
            }
            return false;
        }
    }
    
    public function search($filters = array(), $options = array()) {
        if (empty($options['indexedby'])) {
            $options = array_merge($options, array('indexedby' => 'id'));
        }
        $this->_loaded = false;
        $r = $this->repository->search($filters, $options);
        if (array_key_exists('found_rows', $r)) {
            $this->_found_rows = $r['found_rows'];
        }
        
        if (!empty($options['object'])) {
            $items = array();
            
            $class_name = get_class($this);
            foreach ($r['items'] as $item_key => $item) {
                $o = new $class_name();
                $o->fromArray($item);
                $items[$item_key] = $o;
            }
        } else {
            $items = $r['items'];
        }
        return $items;
    }
    
    public function delete($parms) {
        return $this->repository->delete($parms);
    }
    
    // Getter for model fields
    public function __get($name) {
        $vars = get_object_vars($this);
        if (array_key_exists($name, $vars)) {
            return $this->{$name};
        }
        
        if (in_array($name, $this->_fields)) {
            return array_key_exists($name, $this->attributes) ? $this->attributes[$name] : NULL;
        }
        
        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
    }
    
    // Setter for model fields
    public function __set($name, $value) {
        $vars = get_object_vars($this);
        if (array_key_exists($name, $vars)) {
            $this->{$name} = $value;
        } elseif (in_array($name, $this->_fields)) {
            $this->attributes[$name] = $value;
        } else {
            $trace = debug_backtrace();
            trigger_error(
                'Undefined property via __set(): ' . $name .
                ' in ' . $trace[0]['file'] .
                ' on line ' . $trace[0]['line'],
                E_USER_NOTICE);
        }
    }
    
    public function __isset($name) {
        return isset($this->attributes[$name]);
    }
    
    // Getter for all model fields
    public function toArray() {
        $r = array_fill_keys($this->_fields, NULL);
        foreach ($r as $field => $value) {
            if (array_key_exists($field, $this->attributes)) {
                if (is_array($this->attributes[$field])) {
                    $current_item = array();
                    foreach ($this->attributes[$field] as $attr_key => $attr) {
                        if (is_object($attr) && is_subclass_of($attr, 'Model')) {
                            $current_item[$attr_key] = $attr->toArray();
                        } else {
                            $current_item[$attr_key] = $attr;
                        }
                    }
                    $r[$field] = $current_item;
                } else {
                    $r[$field] = $this->attributes[$field];
                }
            }
        }
        return $r;
    }
    
    public function count($filters = array()) {
        return $this->repository->count($filters);
    }
    
    public function fromArray($a) {
        foreach ($this->_fields as $k) {
            $this->attributes[$k] = array_key_exists($k, $a) ? $a[$k] : NULL;
        }
    }
}
?>
