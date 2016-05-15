<?php

namespace App\Modules\Admin\Components\SourceForm;


interface SourceFormFactory
{
	/**
	 * @return SourceForm
	 */
	public function create();
}