<?php

namespace App\Components;

use Kdyby\Translation\Translator;
use Nette\Application\UI\Control;


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
		$file = dirname($refl->getFileName()) . '/' . lcfirst($refl->getShortName()) . '.latte';
		return file_exists($file) ? $file : NULL;
	}


	function render()
	{
		$this->template->render();
	}
}