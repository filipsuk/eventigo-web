<?php

namespace App\Components;

use Kdyby\Translation\Translator;
use Nette\Application\UI\Control;
use Nette\Utils\DateTime;


abstract class BaseControl extends Control
{
	/** @var \Kdyby\Translation\Translator */
	protected $translator;


	function __construct(Translator $translator)
	{
		$this->translator = $translator;
	}


	protected function createTemplate()
	{
		$template = parent::createTemplate();

		if ($file = $this->getTemplateDefaultFile()) {
			$template->setFile($file);
		}

		$template->addFilter('datetime', function (DateTime $a, DateTime $b = null) {
			$result = $a->format('j. n. ');
			if ($b && $a != $b) {
				$result .= '&nbsp;&ndash;&nbsp;' . $b->format('j. n. ');
			}
			return $result . $b->format('Y');
		});

		return $template;
	}


	/**
	 * Derives template path from class name.
	 *
	 * @return null|string
	 */
	protected function getTemplateDefaultFile()
	{
		$refl = $this->getReflection();
		$file = dirname($refl->getFileName()) . '/' . $refl->getShortName() . '.latte';
		return file_exists($file) ? $file : NULL;
	}


	public function render()
	{
		$this->template->render();
	}
}