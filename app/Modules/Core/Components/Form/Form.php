<?php declare(strict_types=1);

namespace App\Modules\Core\Components\Form;


class Form extends \Nette\Application\UI\Form
{
	/**
	 * @inheritdoc
	 */
	public function __construct(\Nette\ComponentModel\IContainer $parent = NULL, $name = NULL)
	{
		parent::__construct($parent, $name);

		$renderer = $this->getRenderer();
		$renderer->wrappers['label']['container'] = 'th class="th-label"';
		$renderer->wrappers['control']['container'] = 'td class="td-control"';
	}
}