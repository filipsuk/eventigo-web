<?php declare(strict_types=1);

namespace App\Modules\Core\Components\Form;

use Nette\Application\UI\Form as NativeForm;

class Form extends NativeForm
{
	public function __construct(\Nette\ComponentModel\IContainer $parent = NULL, $name = NULL)
	{
		parent::__construct($parent, $name);

		$renderer = $this->getRenderer();
		$renderer->wrappers['label']['container'] = 'th class="th-label"';
		$renderer->wrappers['control']['container'] = 'td class="td-control"';
	}
}