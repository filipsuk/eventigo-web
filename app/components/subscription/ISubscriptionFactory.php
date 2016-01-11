<?php

namespace App\Components\Subscription;


interface ISubscriptionFactory
{
	/**
	 * @return Subscription
	 */
	function create();
}
