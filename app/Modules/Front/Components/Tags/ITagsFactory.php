<?php

namespace App\Modules\Front\Components\Tags;


interface ITagsFactory
{
	/**
	 * @return Tags
	 */
	public function create();
}
