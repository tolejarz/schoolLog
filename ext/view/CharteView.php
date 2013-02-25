<?php
class CharteView extends HtmlView {
    function show($viewparms = array()) {
        $parms = $viewparms;
        $this->_pushTemplate('templates/charte.php', $parms);
    }
}
?>
