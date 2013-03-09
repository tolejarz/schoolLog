<?php
class MySQLConnector {
    public $_link;
    private $_charset;
    
    const CHARSET_UTF8 = 'utf8';
    const CHARSET_LATIN1 = 'latin1';
    
    public function __construct($host, $username, $password, $database, $charset = self::CHARSET_UTF8) {
        $this->_link = new mysqli($host, $username, $password, $database);
        $this->_link->set_charset($charset);
        $this->_charset = $charset;
    }
    
    // dev
    public function getLink() {
        $configurator = Configurator::getInstance('config/config.json');
        $conf = $configurator->getConfiguration();
        $this->_link = new MySQLiDBO($conf['database']['schoollog']['host'], $conf['database']['schoollog']['username'], $conf['database']['schoollog']['password'], $conf['database']['schoollog']['database']);
        return $this->_link;
    }
    // ! dev
    
    public function __destruct() {
        //$this->_link->close();
    }
    
    public function fetchAll() {
        $args = func_get_args();
        $a = array();
        foreach ($args as $arg) {
            if (is_array($arg)) {
                foreach ($arg as $arg_part) {
                    $a[] = $arg_part;
                }
            } else {
                $a[] = $arg;
            }
        }
        $args = $a;
        
        // Replace '%b', '%d', '%i', '%s' with '?', only if not preceded by another '%'
        $p = preg_match_all('/([^%]|^)%([idsb])/', $args[0], $matches, PREG_SET_ORDER);
        $args[0] = preg_replace('/([^%]|^)%[idsb]/', '\1?', $args[0]);
        
        // Replace %%x with %x (in order to not allow %x in 'like' clause)
        $args[0] = preg_replace('/%(%[idsb])/', '\1', $args[0]);
        
        $q = $this->_link->prepare($args[0]);
        if (!empty($this->_link->error)) {
            error_log($this->_link->error, 0);
            error_log($args[0], 0);
            return false;
        }
        
        // Build mysqli_stmt::bind_param first arg (ex.: 'ssiisi')
        $params = array('');
        foreach ($matches as $i => $match) {
            $params[0] .= $match[2];
        }
        
        // Build mysqli_stmt::bind_param second arg (query params as references)
        foreach ($args as $i => $arg) {
            if (!$i) continue;
            $params[] = &$args[$i];
        }
        
        // Call mysqli_stmt::bind_param
        if (count($params) > 1) {
            call_user_func_array(array($q, 'bind_param'), $params);
        }
        
        // Bind result
        $results = array_fill(0, $q->field_count, null);
        $_binded_results = array();
        for ($i = 1; $i <= $q->field_count; $i++) {
            $_binded_results[] = &$results[$i];
        }
        call_user_func_array(array($q, 'bind_result'), $_binded_results);
        
        // Execution
        $q->execute();
        
        // Fetch metadata
        $metadata = $q->result_metadata();
        $fields = $metadata->fetch_fields();
        
        // Fetch results
        $r = array();
        while ($w = $q->fetch()) {
            $_results = array();
            foreach ($_binded_results as $i => $_result) {
                $_results[$fields[$i]->name] = $_result;
            }
            $r[] = $_results;
        }
        $q->free_result();
        $q->close();
        return $r;
    }
    
    public function fetchOne() {
        $args = func_get_args();
        $args[0] .= ' limit 1';
        $r = call_user_func_array(array($this, 'fetchAll'), $args);
        return isset($r[0]) ? $r[0] : $r;
    }
    
    public function insert() {
        $args = func_get_args();
        $p = preg_match_all('/%([idsb])/', $args[0], $matches, PREG_SET_ORDER);
        $args[0] = preg_replace('/%[idsb]/', '?', $args[0]);
        $q = $this->_link->prepare($args[0]);
        // Build mysqli_stmt::bind_param first arg (ex.: 'ssiisi')
        $params = array('');
        foreach ($matches as $i => $match) {
            $params[0] .= $match[1];
        }
        // Faking the first param to be the query (null here) to use the regular check
        if (is_array($args[1])) {
            $args = array_merge(array($args[0]), $args[1]);
        }
        // Build mysqli_stmt::bind_param second arg (query params as references)
        foreach ($args as $i => $arg) {
            if (!$i) continue;
            $params[] = &$args[$i];
        }
        
        // Call mysqli_stmt::bind_param
        call_user_func_array(array($q, 'bind_param'), $params);
        // Execution
        $q->execute();
        $r = array(
            'affected_rows' => $q->affected_rows,
            'insert_id' => $q->insert_id,
        );
        $q->close();
        return $r;
    }
    
    public function update() {
        $args = func_get_args();
        $r = call_user_func_array(array($this, 'insert'), $args);
        unset($r['insert_id']);
        return $r;
    }
    
    public function delete() {
        $args = func_get_args();
        return call_user_func_array(array($this, 'insert'), $args);
    }
}
?>
