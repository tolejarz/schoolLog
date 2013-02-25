<?php
abstract class HtmlView extends View {
	function _pushTemplate($template, $parms = array()) {
		require($template);
	}
}
?>
