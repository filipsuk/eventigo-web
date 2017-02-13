<?php declare(strict_types=1);

namespace App\Modules\Admin\Components\SourceForm;


interface SourceFormFactory
{
	/**
	 * @return SourceForm
	 */
	public function create();
}