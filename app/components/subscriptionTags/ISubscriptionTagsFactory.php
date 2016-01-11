<?php

namespace App\Components\SubscriptionTags;


interface ISubscriptionTagsFactory
{
	/**
	 * @return SubscriptionTags
	 */
	function create();
}
