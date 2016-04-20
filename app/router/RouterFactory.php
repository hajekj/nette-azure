<?php

namespace App;

use Nette;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;


class RouterFactory
{

	/**
	 * @return Nette\Application\IRouter
	 */
	public static function createRouter()
	{
		$router = new RouteList;

        $router[] = new Route('auth/logout', 'Authentication:logout');
		$router[] = new Route('auth/<strategy>/callback', 'Authentication:callback');
		$router[] = new Route('auth/<strategy>', 'Authentication:auth');
        $router[] = new Route('imgs', 'Images:images');

		$router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');

		return $router;
	}

}
