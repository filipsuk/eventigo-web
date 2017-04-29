<?php declare(strict_types=1);

namespace App\Modules\Front\Components\Tags;

interface TagsFactoryInterface
{
	public function create(): Tags;
}
