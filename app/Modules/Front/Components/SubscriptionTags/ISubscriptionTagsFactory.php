<?php declare(strict_types=1);

namespace App\Modules\Front\Components\SubscriptionTags;


interface ISubscriptionTagsFactory
{
	/**
	 * @return SubscriptionTags
	 */
	public function create();
}
