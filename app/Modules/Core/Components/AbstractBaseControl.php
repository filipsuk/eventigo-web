<?php declare(strict_types=1);

namespace App\Modules\Core\Components;

use App\Modules\Core\Utils\DateTime;
use Kdyby\Translation\Translator;
use Nette\Application\UI\Control;
use Nette\Bridges\ApplicationLatte\Template;


abstract class AbstractBaseControl extends Control
{
	/**
	 * @var \Kdyby\Translation\Translator
	 */
	protected $translator;


	public function __construct(Translator $translator)
	{
		$this->translator = $translator;
	}


	protected function createTemplate(): Template
	{
		/** @var Template $template */
		$template = parent::createTemplate();

		if ($file = $this->getTemplateDefaultFile()) {
			$template->setFile($file);
		}

		$template->addFilter('datetime', function (DateTime $a, ?DateTime $b = null) {
			DateTime::setTranslator($this->translator);
			return DateTime::eventsDatetimeFilter($a, $b);
		});

		return $template;
	}


	/**
	 * Derives template path from class name.
	 */
	protected function getTemplateDefaultFile(): ?string
	{
		$refl = $this->getReflection();
		$file = dirname($refl->getFileName()) . '/' . $refl->getShortName() . '.latte';
		return file_exists($file) ? $file : NULL;
	}


	public function render(): void
	{
		$this->template->render();
	}
}
