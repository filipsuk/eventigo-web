<?php declare(strict_types=1);

namespace App\Modules\Front\Components\Tags;


interface ITagsFactory
{
	public function create(): Tags;
}
