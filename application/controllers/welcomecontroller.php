<?php

namespace Application\Controllers;
use System\Core\Router;

class WelcomeController extends \System\Core\Controller {
	public function __construct() {
		parent::__construct();
		// Do any other stuff here, set up models etc.
	}

	public function index() {
		echo "Hello, world!<br/>\n";
	}
}