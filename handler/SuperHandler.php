<?php
class SuperHandler extends Handler {
    public function checkSession() {
        if (!isset($_SESSION['user']['id'])) {
            $auth_handler = new AuthHandler($this->args, $this->settings, $this->dbo);
            $auth_handler->doLogin();
            die();
        }
        if (empty($_SESSION['user']['charter'])) {
            $auth_handler = new AuthHandler($this->args, $this->settings, $this->dbo);
            $auth_handler->doCharte();
            die();
        }

        if(isset($_GET["numero"]))
        {
            if($_GET['redir']){
                header("Location: index.php?numero=".$_GET['numero']);
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
    }
}
?>
