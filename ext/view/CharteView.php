<?php
class CharteView extends HtmlView {
    function show($viewparms = array()) {
        $parms = $viewparms;
        $this->_pushTemplate('templates/auth/charte.phtml', $parms);
    }
}
?>
