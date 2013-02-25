<?php
function import($classname) {
	if (substr($classname, -3) == 'DBO') {
		include_once('core/db/DBO.php');
		include_once(sprintf('ext/db/%s.php', $classname));
	} else if (substr($classname, -5) == 'Model') {
		include_once('core/db/DBO.php');
		include_once('core/model/Model.php');
		include_once(sprintf('ext/model/%s.php', $classname));
		if (is_file(sprintf('ext/model/%sException.php', $classname))) {
			include_once(sprintf('ext/model/%sException.php', $classname));
		}
	} else if (substr($classname, -10) == 'Controller') {
		include_once('core/db/DBO.php');
		include_once('core/controller/Controller.php');
		include_once(sprintf('ext/controller/%s.php', $classname));
	} else if (substr($classname, -4) == 'View') {
		include_once('core/view/View.php');
		include_once('core/view/HtmlView.php');
		include_once(sprintf('ext/view/%s.php', $classname));
	} else if ($classname == 'Mail') {
		include_once('mail/Mail.php');
	} else {
		if (is_file(sprintf('core/%s.php', $classname))) {
			include_once(sprintf('core/%s.php', $classname));
		}
		if (is_file(sprintf('lib/_inc/%s.php', $classname))) {
			include_once(sprintf('lib/_inc/%s.php', $classname));
		}
	}
}
?>