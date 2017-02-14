<?php declare(strict_types=1);

namespace App\Modules\Admin\Components\SourceForm;


interface SourceFormFactory
{
	public function create(): SourceForm;
}
