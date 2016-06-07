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
		$newsletterRouter[] = new Route('newsletter/<hash [a-z0-9]{32}>', 'Newsletter:default', Route::SECURED);
		$newsletterRouter[] = new Route('newsletter/dynamic/<userId>', 'Newsletter:dynamic', Route::SECURED);
		$newsletterRouter[] = new Route('newsletter/<action>/<hash>', 'Newsletter:default', Route::SECURED);
		$router[] = $newsletterRouter;

		// Front
		$router[] = new Route('profile/settings/<token>', 'Front:Profile:settings');
		$router[] = new Route('discover/?', 'Front:Homepage:discover');
		$router[] = new Route('<presenter>/<action>[/<id>]', [
			'module' => 'Front',
			'presenter' => 'Homepage',
			'action' => 'default'
		]);
		return $router;
	}

}
