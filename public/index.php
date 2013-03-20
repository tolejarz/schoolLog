<?php
// TO REMOVE
function week_dates($week, $year) {
    $week_dates = array();
    // Get timestamp of first week of the year
    $first_day = mktime(12, 0, 0, 1, 1, $year);
    $first_week = date('W', $first_day);
    
    if ($first_week > 1) {
        $first_day = strtotime('+1 week', $first_day); // skip to next if year does not begin with week 1
    }
    // Get timestamp of the week
    $timestamp = strtotime("+$week week", $first_day);

    // Adjust to Monday of that week
    $what_day = date('w', $timestamp); // I wanted to do "N" but only version 4.3.9 is installed :-(
    if ($what_day==0) {
        // actually Sunday, last day of the week. FIX;
        $timestamp = strtotime("-6 days",$timestamp);
    } elseif ($what_day > 1) {
        $what_day--;
        $timestamp = strtotime("-$what_day days",$timestamp);
    }
    $week_dates[0] = $timestamp;                            // Monday
    $week_dates[1] = strtotime("+1 day", $timestamp);       // Tuesday
    $week_dates[2] = strtotime("+2 day", $timestamp);       // Wednesday
    $week_dates[3] = strtotime("+3 day", $timestamp);       // Thursday
    $week_dates[4] = strtotime("+4 day", $timestamp);       // Friday
    $week_dates[5] = strtotime("+5 day", $timestamp);       // Saturday
    $week_dates[6] = strtotime("+6 day", $timestamp);       // Sunday
    return($week_dates);
}

function getFrMonth($month) {
    $months = array('janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre');
    return $months[($month - 1) % 12];
}

function ldapAuth($server, $login, $password) {
    // Connexion au serveur LDAP
    $ldapserver = ldap_connect($server);
    if (!$ldapserver) return null;
    
    //Definition des options
    ldap_set_option($ldapserver, LDAP_OPT_PROTOCOL_VERSION, 3);
    $level=ini_get('display_errors');
    ini_set('display_errors',0);
    
    // Authentification
    $bind = ldap_bind($ldapserver, 'EPSI\\' . $login, $password);
    ini_set('display_errors',$level);

    if(!empty($bind)) {
        //recuperation du nom et du prenom
        $sr=ldap_search($ldapserver,"dc=epsi,dc=fr", "sAMAccountName=".$login);
        $info=ldap_get_entries($ldapserver, $sr);
        
        if ($info["count"] == 0) return NULL;
        
        $ret['nom'] = $info[0]['sn'][0];
        $ret['email']= $info[0]['mail'][0];
        $ret['prenom'] = $info[0]['givenname'][0];
        
        $dn = $info[0]['dn'];
        $str = strstr($dn,"OU=") ;
        $i = strpos($str,",") ;
        $classe = utf8_decode(substr($str,3,$i-3));
        
        /* récupération de la classe (les BTS ont un libellé différent de celui de notre base, donc on convertit */
        $ret['classe'] = ($classe == '1er année' ? 'BTS / CPI 1' : ($classe == '2ème année' ? 'BTS / CPI 2' : $classe)); 
        
        if (preg_match("!OU=Prof!", $dn)) {
            $ret['droits'] = 'enseignant';
        } else if (preg_match("!OU=Special!", $dn) && strtolower($login) == "adminschoollog") {
            $ret['droits'] = 'administrateur';
        } else if (preg_match("!OU=Eleves!", $dn)) {
            $ret['droits'] = 'eleve';
        } else if (preg_match("!OU=Special!", $dn)){
            $ret['droits'] = 'superviseur';
        }
        
        //fermeture de la connexion
        ldap_close($ldapserver);
        return $ret; 
    }
    return null;
}

function recupererInfos($server, $login){

    // Connexion au serveur LDAP
    $ldapserver = ldap_connect($server);
    if (!$ldapserver) return null;
    
    //Definition des options
    ldap_set_option($ldapserver, LDAP_OPT_PROTOCOL_VERSION, 3);
    $level=ini_get('display_errors');
    ini_set('display_errors',0);

    //Authentification
    $bind = ldap_bind($ldapserver,"EPSI\\"."scout","scout");
    ini_set('display_errors',$level);

    if($bind) {
        //recuperation du nom et du prenom
        $sr=ldap_search($ldapserver,"dc=epsi,dc=fr", "sAMAccountName=".$login);
        $info=ldap_get_entries($ldapserver, $sr);
        
        if ($info["count"]==0) return NULL;
        
        $ret['nom'] = $info[0]['sn'][0];
        $ret['email']= $info[0]['mail'][0];
        $ret['prenom'] = $info[0]['givenname'][0];
        
        $dn = $info[0]['dn'];
        $str = strstr($dn,"OU=") ;
        $i = strpos($str,",") ;
        $classe = utf8_decode(substr($str,3,$i-3));
        
        /* récupération de la classe (les BTS ont un libellé différent de celui de notre base, donc on convertit */
        $ret['classe'] = ($classe == '1er année' ? 'BTS / CPI 1' : ($classe == '2ème année' ? 'BTS / CPI 2' : $classe)); 

        if (preg_match("!OU=Prof!", $dn)) {
            $ret['droits'] = 'enseignant';
        } else if (preg_match("!OU=Special!", $dn) && strtolower($login) == "adminschoollog") {
            $ret['droits'] = 'administrateur';
        } else if (preg_match("!OU=Eleves!", $dn)) {
            $ret['droits'] = 'eleve';
        } else if (preg_match("!OU=Special!", $dn)){
            $ret['droits'] = 'superviseur';
        }
        
        //fermeture de la connexion
        ldap_close($ldapserver);
        return $ret; 
    } else {   
        $ret["error_connex"] = (!empty($level) ? 'CONNECTION_FAILED' : 'BAD_LOGIN_MDP');
        return $ret;    
    }
    return NULL;
}
// ! TO REMOVE
?>

<?php
chdir('..');

session_start();

require_once 'bootstrap.php';

$configurator = Configurator::getInstance('config/config.json');

$action = new Action();
$action->perform($configurator->getConfiguration());
?>
