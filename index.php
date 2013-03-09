<?php
session_start();

require_once 'bootstrap.php';

$configurator = Configurator::getInstance('config/config.json');

$action = new Action();
$action->perform($configurator->getConfiguration());
?>
