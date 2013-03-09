<?php
class AuthController extends Controller {
    public function doLogin() {
        if (!empty($_SESSION['user_id'])) {
            $this->defaultRoute();
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (empty($_POST['login'])) {
                $_SESSION['ERROR_MSG'] = 'Veuillez remplir le champ <b>Login</b>';
            } elseif (empty($_POST['password'])) {
                $_SESSION['ERROR_MSG'] = 'Veuillez remplir le champ <b>Mot de passe</b>';
            } else {
                $u = new UserModel();
                $login = trim($_POST['login']);
                $pass = $_POST['password'];
                //$r = $u->ldapAuth($login, $pass);
                $r = $u->auth($login, $pass);
                if ($r == 'BAD_LOGIN_MDP') {
                    $_SESSION['ERROR_MSG'] = '<b>Login</b> ou <b>Mot de passe</b> incorrect';
                } elseif ($r == 'CONNECTION_FAILED') {
                    $_SESSION['ERROR_MSG'] = '<b>Connexion échouée</b>';
                } elseif (!empty($r['auth'])) {
                    $_SESSION['user_id']                    = $r['user_id'];
                    $_SESSION['user_login']                 = $r['user_login'];
                    $_SESSION['user_civility']              = $r['user_civility'];
                    $_SESSION['user_surname']               = $r['user_surname'];
                    $_SESSION['user_name']                  = $r['user_name'];
                    $_SESSION['user_email']                 = $r['user_email'];
                    $_SESSION['user_lastlog']               = $r['user_lastlog'];
                    $_SESSION['user_class']                 = $r['user_class'];
                    $_SESSION['user_classes_subjects']      = $r['user_classes_subjects'];
                    $_SESSION['user_privileges']            = $r['user_privileges'];
                    $_SESSION['user_lastlog']               = $r['user_lastlog'];
                    $_SESSION['user_charter']               = $r['user_charter'];
                    
                    $this->defaultRoute();
                }
            }
        } else {
            if(isset($_GET["redir"])){
                header("Location: ". SITE_DIR ."index.php?numero=".$_GET["numero"]);
                die();
            }
            if(!empty($_GET["numero"]) && $_GET["numero"]=="403"){
                $_SESSION['ERROR_MSG'] = "L'accès à cette zone vous est interdit";
            } elseif(!empty($_GET["numero"]) && $_GET["numero"]=="404"){
                $_SESSION['ERROR_MSG'] = "Page non trouvée";
            }
        }
        
        $v = new LoginView();
        $v->show(array());
    }
    
    private function defaultRoute() {
        if (in_array($_SESSION['user_privileges'], array('enseignant', 'eleve'))) {
            Router::redirect('Calendar');
        } elseif ($_SESSION['user_privileges'] == 'superviseur') {
            Router::redirect('CalendarRequestList');
        } elseif ($_SESSION['user_privileges'] == 'administrateur') {
            Router::redirect('BackupList');
        }
    }
    
    public function doLogout() {
        session_destroy();
        Router::redirect('Root');
    }
    
    public function doCharte() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['validation'])) {
                $user = new UserModel();
                $user->update($_SESSION['user_id'], array('charte_signee' => 1));
                $_SESSION['user_charter'] = true;
                $this->defaultRoute();
            }
        }
        $v = new CharteView();
        $v->show(array());
    }
}
?>
