<?php

namespace App\Modules\Front\Components\SubscriptionTags;


interface ISubscriptionTagsFactory
{
	/**
	 * @return SubscriptionTags
	 */
	public function create();
}
