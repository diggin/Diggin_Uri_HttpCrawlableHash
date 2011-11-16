<?php
require_once __DIR__. '/vendor/Zend/Loader/StandardAutoloader.php';
use Zend\Loader\StandardAutoloader;

$autoloader = new StandardAutoloader;
$autoloader->registerNamespace("Diggin\\Uri", dirname(__DIR__).'/src/Diggin/Uri');
$autoloader->register();
