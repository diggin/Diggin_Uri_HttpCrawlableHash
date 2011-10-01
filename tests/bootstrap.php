<?php
$zf2 = $_SERVER['HOME']. '/dev/zendframework_zf2/library';
require_once $zf2. '/Zend/Loader/StandardAutoloader.php';
use Zend\Loader\StandardAutoloader;

$autoloader = new StandardAutoloader;
$autoloader->registerNamespace("Diggin\\Uri", dirname(__DIR__).'/src/Diggin/Uri');
$autoloader->register();
