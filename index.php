<?php

ini_alter('display_errors', 'On');
define('ENVIRONMENT', 'development');

require_once 'system/core/framework.php';

System\Core\Framework::init();

// Check if PATH_INFO is set...
if (isset($_SERVER['PATH_INFO']))
	System\Core\Router::set_url($_SERVER['PATH_INFO']);
else
	System\Core\Router::set_url('/');

System\Core\Framework::process();