<?php
class Uri {
    private $uri;
    private $arguments;
    
    public function __construct() {
        $c = file_get_contents('routes/uri.json');
        $this->uri = json_decode($c, true);
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
        $url_path = $url_path[0];
        $url_matched = false;
        
        $request_method = !empty($_SERVER['REQUEST_METHOD']) ? strtolower(trim($_SERVER['REQUEST_METHOD'])) : NULL;
        
        
//error_log('URI: ' . $url_path);

require_once('lib/epsilib.php');
require_once('config.php');

//
$configurator = Configurator::getInstance('config/config.json');
$conf = $configurator->getConfiguration();
//
$dbo = new MySQLiDBO($conf['database']['schoollog']['host'], $conf['database']['schoollog']['username'], $conf['database']['schoollog']['password'], $conf['database']['schoollog']['database']);

session_start();

if (strpos($url_path, 'resource') === false) {
    if (!isset($_SESSION['user_id'])) {
        $controller = new AuthController($dbo);
        $controller->perform('login');
        die();
    }
    if (empty($_SESSION['user_charter'])) {
        $controller = new AuthController($dbo);
        $controller->perform('charte');
        die();
    }
}

if(isset($_GET["numero"]))
{
    if($_GET['redir']){
        header("Location: " . SITE_DIR . "index.php?numero=".$_GET['numero']);
        die();
    }
    if ($_GET["numero"] == '404') {
        $_SESSION['ERROR_MSG'] = "Page non trouvée";
    } else if ($_GET["numero"] == '403') {
        $_SESSION['ERROR_MSG'] = "L'accès à cette zone vous est interdit";
    } else {
        $_SESSION['ERROR_MSG'] = "Une erreur est survenue, essayez de rafraîchir la page ou de réessayer plus tard";
    };
}

/* récupération de l'année scolaire en cours */
$year = date('m') >= 9 ? date('Y') : date('Y') - 1;
define('CURRENT_PROMOTION', $year);
/* récupère le numéro de la semaine du 31 août de l'annéde la promo actuelle */
define('LAST_WEEK', date('W', mktime(12, 0, 0, 8, 31, CURRENT_PROMOTION)));


/* ----------------------------------------------------------------------------- */

$page = $action = '';
if (!empty($_GET['page'])) {
    $page = $_GET['page'];
} /*else {
    if ($_SESSION['user_privileges'] == 'eleve') {
        header('Location: index.php?page=emploi_du_temps');
        die();
    }
    if ($_SESSION['user_privileges'] == 'enseignant') {
        header('Location: index.php?page=emploi_du_temps');
        die();
    }
    if ($_SESSION['user_privileges'] == 'superviseur') {
        header('Location: index.php?page=emploi_du_temps&action=demande');
        die();
    }
    if ($_SESSION['user_privileges'] == 'administrateur') {
        header('Location: index.php?page=sauvegardes');
        die();
    }
}*/
/*
$pages = array(
    'classes'           => 'ClasseController',
    'emploi_du_temps'   => 'CalendrierController',
    'materiels'         => 'MaterielController',
    'matieres'          => 'MatiereController',
    'matieresClasse'    => 'MatieresClasseController',
    'reservations'      => 'ReservationController',
    'sauvegardes'       => 'SauvegardeController',
    'supports'          => 'SupportController',
    'utilisateurs'      => 'UserController',
);
if (array_key_exists($page, $pages)) {
    $controller = new $pages[$page]($dbo);
    $controller->perform();
}
*/
        foreach ($this->uri as $action => $query_params) {
            if (array_key_exists('uri', $query_params)
                && strpos($query_params['uri'], '?') === false
                && preg_match('`^' . $query_params['uri'] . '$`', $url_path, $matches)) {
                $url_matched = true;
                
                $handler = explode('.', $query_params['handler']);
                $handler_class = $handler[0];
                $handler_method = 'do' . ucfirst($handler[1]);
                
                
                $args = array();
                array_shift($matches);
                foreach ($query_params['args'] as $i => $arg) {
                    if (!isset($matches[$i])) {
                        $args[$arg] = null;
                    } else {
                        $args[$arg] = urldecode(urldecode($matches[$i]));
                    }
                }
                
                $handler = new $handler_class($args, $conf, $dbo);
ob_start();
                $handler->$handler_method();
$html = ob_get_contents();
ob_end_clean();
                
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
        
        
        elseif ($handler_class !== 'ResourceHandler') {
            include_once 'templates/layout/main.phtml';
        } else {
            echo $html;
        }
        
        return $query;
    }
}
?>
