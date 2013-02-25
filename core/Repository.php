<?php
abstract class Repository {
    protected $link;
    protected $table_name = null;
    protected $primary_key = null;
    protected $fields = null;
    
    public function __construct() {
        $this->link = Configurator::getInstance()->getConnection('campaign');
        foreach ($this->fields as $field_name => $field_description) {
            if (!empty($field_description['primary'])) {
                $this->primary_key = $field_name;
            }
        }
    }
    
    protected function build_params($fields) {
        $params = array();
        foreach ($this->fields as $field_name => $field_specs) {
            // TODO: separate real fields and "join" fields
            if (strpos($field_name, '.')) continue;
            
            // Set field to NULL if nullable
            if ($fields[$field_name] === NULL && $field_specs['nullable']) {
                $params[] = 'NULL';
            // Set field
            } else {
                $params[] = $field_specs['type'];
            }
        }
        return $params;
    }
    
    protected function build_fields($fields) {
        $f = array();
        foreach ($this->fields as $field_name => $field_specs) {
            // TODO: separate real fields and "join" fields
            if (strpos($field_name, '.')) continue;
            
            // Set field to NULL if nullable
            if ($fields[$field_name] === NULL && $field_specs['nullable']) {
                
            // Set field (casting it with its type)
            } else {
                switch ($field_specs['type']) {
                    case '%d': $v = (float) $fields[$field_name]; break;
                    case '%i': $v = (int) $fields[$field_name]; break;
                    case '%s': $v = (string) $fields[$field_name]; break;
                    default: $v = $fields[$field_name];
                }
                $values[] = $v;
            }
            $f[] = '`' . $field_name . '`';
        }
        return array('fields' => $f, 'values' => $values);
    }
    
    // TODO : générer l'autoincrement dans le bon champ
    public function create($a) {
        if (array_key_exists($this->primary_key, $a) && empty($a[$this->primary_key])) {
            $a[$this->primary_key] = NULL;
        }
        
        $bf = $this->build_fields($a);
        $fields = $bf['fields'];
        $values = $bf['values'];
        
        $params = $this->build_params($a);
        
        $query = 'insert into `' . $this->table_name . '` (' . implode(',', $fields) . ') values (' . implode(',', $params) . ')';
        $r = $this->link->insert($query, $values);
        return $r;
    }
    
    // TODO : ne pas re-setter la primary key (ne sert pas à grand chose mais bon...)
    public function update($a) {
        $bf = $this->build_fields($a);
        $fields = $bf['fields'];
        $values = $bf['values'];
        
        $values[] = $a[$this->primary_key];
        
        $fields_types = $this->build_params($a);
        
        $fields = array_combine($fields, $fields_types);
        $params = array();
        foreach ($fields as $field => $type) {
            $params[] = $field . '=' . $type;
        }
        
        $query = 'update `' . $this->table_name . '` set ' . implode(',', $params) . ' where `' . $this->primary_key . '`=' . $this->fields[$this->primary_key]['type'];
        $r = $this->link->update($query, $values);
        return $r;
    }
    
    public function delete($filters) {
        $filters = $this->build_filters($filters);
        $r = $this->link->delete('delete from `' . $this->table_name . '` where ' . implode(' and ', $filters['fields']), $filters['values']);
        return $r['affected_rows'] == '1';
    }
    
    public function get($key, $value, $options) {
        if (is_array($value)) {
            return $this->find($value, $options);
        }
        
        if ($key === NULL) $key = $this->primary_key;
        
        $query = !empty($options['query']) ? $options['query'] : 'select * from `' . $this->table_name . '`';
        if (strpos($key, '.')) {
            $key_parts = explode('.', $key);
            $field = '`' . $key_parts[0] . '`.`' . $key_parts[1] . '`';
        } else {
            $field = '`' . $this->table_name . '`.`' . $key . '`';
        }
        
        $filters = $this->build_filters(array($key => $value));
        
        $query .= ' where ' . implode(' and ', $filters['fields']);
        $r = $this->link->fetchOne($query, $filters['values']);
        return $r;
    }
    
    private function find($filters, $options) {
        $filters = $this->build_filters($filters);
        $where = $filters['fields'];
        $where_v = $filters['values'];
        
        if (!empty($options['query'])) {
            $query = $options['query'];
        } else {
            $query = 'select * from `' . $this->table_name . '`';
        }
        
        // Set "where" clause
        $query .= (!empty($where) ? ' where ' . implode(' and ', $where) : '');
        return $this->link->fetchOne($query, $where_v);
    }
    
    private function build_filters($filters) {
        $where = $where_v = array();
        foreach ($filters as $key => $value) {
            // Operator specified
            if (strpos($key, ' ') !== false) {
                $key_parts = explode(' ', $key);
                list($key, $operator) = $key_parts;
            // No operator specified (default: "=")
            } else {
                $operator = '=';
            }
            
            // Table name specified
            if (strpos($key, '.') !== false) {
                $field = $key;
                $parts = explode('.', $key);
                // This test in order to retrieve "field" instead of "table.field"
                if ($parts[0] != $this->table_name) {
                    $key = $field;
                } else {
                    $key = $parts[1];
                }
            // No table name specified ("table_name" private attribute used)
            } else {
                $field = '`' . $this->table_name . '`.`' . $key . '`';
            }
            
            // Array value
            if (is_array($value)) {
                if ($operator == 'between') {
                    $where[] = $field . ' ' . $operator . ' ' . $this->fields[$key]['type'] . ' and ' . $this->fields[$key]['type'];
                } else {
                    $operator = $operator == '!=' ? 'not in' : 'in';
                    $where[] = $field . ' ' . $operator . ' (' . implode(',', array_fill(0, count($value), $this->fields[$key]['type'])) . ')';
                }
                if (!empty($value)) { // This test is not above, in order to not enter in "scalar mode" if an empty array is passed
                    $where_v = array_merge($where_v, $value);
                } else {
                    $where_v[] = -1;
                }
            // NULL value
            } elseif ($value === NULL) {
                $operator = $operator == '!=' ? 'is not' : 'is';
                $where[] = $field . ' ' . $operator . ' null';
            // Scalar value
            } else {
                if ($operator == 'find') {
                    $where[] = 'find_in_set(' . $this->fields[$key]['type'] . ', ' . $field . ')>0';
                } else {
                    $where[] = $field . ' ' . $operator . ' ' . $this->fields[$key]['type'];
                }
                switch ($this->fields[$key]['type']) {
                    case '%d': $v = (float) $value; break;
                    case '%i': $v = (int) $value; break;
                    case '%s': $v = (string) $value; break;
                    default: $v = $value;
                }
                $where_v[] = $value;
            }
        }
        return array('fields' => $where, 'values' => $where_v);
    }
    
    public function search($filters = array(), $options = array()) {
        $filters = $this->build_filters($filters);
        $where = $filters['fields'];
        $where_v = $filters['values'];
        
        if (!empty($options['count'])) {
            $query = 'select count(*) as `cnt` from ' . $this->table_name;
        } elseif (!empty($options['query'])) {
            $query = $options['query'];
        } else {
            $query = 'select * from `' . $this->table_name . '`';
        }
        
        // Set "where" clause
        $query .= (!empty($where) ? ' where ' . implode(' and ', $where) : '');
        
        // Set "group by" clause
        if (!empty($options['orderby']) && isset($this->fields[$options['orderby']])) {
            $query .= ' order by ' . $options['orderby'] . ' ' . (!empty($options['orderby_dir']) && $options['orderby_dir'] == 'desc' ? 'desc' : 'asc');
        }
        
        // Set "limit" and "offset" clauses
        $query .= (!empty($options['limit']) ? ' limit ' . $options['limit'] : '') . (!empty($options['offset']) ? ' offset ' . $options['offset'] : '');
        
        if (!empty($options['count'])) {
            $r = $this->link->fetchOne($query, $where_v);
            return $r['cnt'];
        }
        $items = $this->link->fetchAll($query, $where_v);
        
        // Build items list
        $r = array();
        if (!isset($options['indexedby'])) {
            $options['indexedby'] = 'id';
        }
        foreach ($items as $item) {
            if (isset($item[$options['indexedby']])) {
                $r[$item[$options['indexedby']]] = $item;
            } else {
                $r[] = $item;
            }
        }
        
        $r = array('items' => $r);
        if (!empty($options['found_rows'])) {
            $r2 = $this->link->fetchOne('select found_rows() as cnt');
            $r['found_rows'] = $r2['cnt'];
        }
        return $r;
    }

    public function count($filters = array()) {
        return $this->search($filters, array('count' => true));
    }
}
?>
