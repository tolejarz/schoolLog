<?php
// No SQL!!! :)
class AuthController extends Controller {
    public function doLogin() {
        if (!empty($_SESSION['user']['id'])) {
            $this->defaultRoute();
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (empty($_POST['login'])) {
                $_SESSION['ERROR_MSG'] = 'Veuillez remplir le champ <b>Login</b>';
            } elseif (empty($_POST['password'])) {
                $_SESSION['ERROR_MSG'] = 'Veuillez remplir le champ <b>Mot de passe</b>';
            } else {
                $user = new UserModel();
                //$r = $u->ldapAuth($login, $pass);
                $r = $user->auth(trim($_POST['login']), $_POST['password']);
                if (!$r) {
                    $_SESSION['ERROR_MSG'] = '<b>Login</b> ou <b>Mot de passe</b> incorrect';
                } else {
                    $_SESSION['user'] = array(
                        'id'                => $user->id,
                        'login'             => $user->login,
                        'civility'          => $user->civility,
                        'surname'           => $user->nom,
                        'name'              => $user->nom,
                        'email'             => $user->email,
                        'lastlog'           => $user->derniere_connexion,
                        'class'             => $user->user_class,
                        'classes_subjects'  => $r['user_classes_subjects'],
                        'privileges'        => $user->droits,
                        'charter'           => $user->charte_signee,
                    );
                    $this->defaultRoute();
                }
            }
        } else {
            if(isset($_GET["redir"])){
                header("Location: index.php?numero=".$_GET["numero"]);
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
        if (in_array($_SESSION['user']['privileges'], array('enseignant', 'eleve'))) {
            Router::redirect('Calendar');
        } elseif ($_SESSION['user']['privileges'] == 'superviseur') {
            Router::redirect('CalendarRequestList');
        } elseif ($_SESSION['user']['privileges'] == 'administrateur') {
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
                $user->get($_SESSION['user']['id']);
                $user->charte_signee = true;
                $user->save();
                
                $_SESSION['user']['charter'] = true;
                $this->defaultRoute();
            }
        }
        $v = new CharteView();
        $v->show(array());
    }
}
?>
