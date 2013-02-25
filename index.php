<?php
// TODO: to remove as soon as possible
function import($n) { return; }
// ! TODO

require_once 'bootstrap.php';

$configurator = Configurator::getInstance('config/config.json');

$action = new Action();
$action->perform($configurator->getConfiguration());
?>
