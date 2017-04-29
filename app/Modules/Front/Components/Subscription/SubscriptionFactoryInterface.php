<?php declare(strict_types=1);

namespace App\Modules\Front\Components\Subscription;

interface SubscriptionFactoryInterface
{
	public function create(): Subscription;
}
