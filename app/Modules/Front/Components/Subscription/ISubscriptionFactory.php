<?php

namespace App\Modules\Front\Components\Subscription;


interface ISubscriptionFactory
{
	/**
	 * @return Subscription
	 */
	public function create();
}
