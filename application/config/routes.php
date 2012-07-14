<?php
namespace Application\Config;
use System\Core\Router;

class Routes extends \System\Core\Config {
	public function init() {
		Router::default_controller('welcome');
		Router::set_404('site404');

		// Route examples:
		// Router::add_route('test', 'welcome/index');
		// Router::add_route('test/(:any)', 'welcome/index/$1');
	}
}