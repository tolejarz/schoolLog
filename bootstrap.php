<?php
function autoload($classname) {
    if (in_array($classname, array('Action', 'Controller', 'DBO', 'MySQLiDBO', 'FileManipulation', 'HtmlView', 'View', 'Formatter', 'Handler', 'Model', 'Model_old', 'Query', 'Repository', 'RestClient', 'Service', 'Uri'))) {
        include_once sprintf('core/%s.php', $classname);
    } elseif (in_array($classname, array('Configurator', 'Mail', 'MySQLConnector', 'HeaderUtils', 'Router'))) {
        include_once sprintf('lib/%s.php', $classname);
    } elseif (substr($classname, -7) == 'Service') {
        include_once sprintf('domain/service/%s.php', $classname);
    } elseif (substr($classname, -4) == 'View') {
        include_once sprintf('ext/view/%s.php', $classname);
    } elseif (substr($classname, -10) == 'Controller') {
        include_once sprintf('ext/controller/%s.php', $classname);
    } elseif (substr($classname, -5) == 'Model') {
        include_once sprintf('ext/model/%s.php', $classname);
    } elseif (substr($classname, -10) == 'Repository') {
        include_once sprintf('ext/model/repository/%s.php', $classname);
    } elseif (substr($classname, -7) == 'Handler') {
        include_once sprintf('handler/%s.php', $classname);
    } elseif(substr($classname, -6) == 'Script') {
        include_once sprintf('scripts/%s.php', $classname);
    } else {
        $classname_parts = preg_split('/(?=[A-Z])/', $classname, -1, PREG_SPLIT_NO_EMPTY);
        $folder = strtolower($classname_parts[0]);
        $root_folder = strtolower($classname_parts[count($classname_parts) - 1]);
        $path = sprintf('action/%s/%s/%s.php', $root_folder, $folder, $classname);
        if (is_file($path)) {
            include_once $path;
        } else {
            $path = sprintf('action/%s/%s.php', $root_folder, $classname);
            if (is_file($path)) {
                include_once $path;
            } else {
                $path = sprintf('action/%s.php', $classname);
                if (is_file($path)) {
                    include_once $path;
                }
            }
        }
    }
    
    // If class does not exist, raise exception
    if (!class_exists($classname)) {
        throw new Exception(sprintf('The class %s does not exist.', $classname));
    }
}
spl_autoload_register('autoload');
?>
