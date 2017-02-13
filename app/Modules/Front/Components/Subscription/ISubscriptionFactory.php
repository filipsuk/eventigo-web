<?php declare(strict_types=1);

namespace App\Modules\Front\Components\Subscription;


interface ISubscriptionFactory
{
	/**
	 * @return Subscription
	 */
	public function create();
}
