<?php

namespace App\Components\Subscription;


interface ISubscriptionFactory
{
	/**
	 * @return Subscription
	 */
	public function create();
}
