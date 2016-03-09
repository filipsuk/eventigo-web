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

		// Admin
		$adminRouter = new RouteList('Admin');
		$adminRouter[] = new Route('admin/<presenter>/<action>[/<id>]', 'Dashboard:default');
		$router[] = $adminRouter;

		// Nesletter
		$newsletterRouter = new RouteList('Newsletter');
		$newsletterRouter[] = new Route('newsletter/<hash>', 'Newsletter:default');
		$router[] = $newsletterRouter;

		// Front
		$router[] = new Route('<presenter>/<action>[/<id>]', 'Front:Homepage:default');
		return $router;
	}

}
