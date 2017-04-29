<?php declare(strict_types=1);

namespace App\Modules\Admin\Components\SourceForm;

interface SourceFormFactoryInterface
{
	public function create(): SourceForm;
}
