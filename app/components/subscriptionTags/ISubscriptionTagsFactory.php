<?php

namespace App\Components\SubscriptionTags;


interface ISubscriptionTagsFactory
{
	/**
	 * @return SubscriptionTags
	 */
	public function create();
}
