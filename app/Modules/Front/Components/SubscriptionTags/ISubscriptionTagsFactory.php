<?php declare(strict_types=1);

namespace App\Modules\Front\Components\SubscriptionTags;


interface ISubscriptionTagsFactory
{
	public function create(): SubscriptionTags;
}
