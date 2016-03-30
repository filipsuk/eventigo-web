<?php

namespace App\Modules\Newsletter\Components\Newsletter;

use App\Modules\Core\Components\BaseControl;
use App\Modules\Newsletter\Model\NewsletterService;
use Kdyby\Translation\Translator;
use Latte\Loaders\StringLoader;
use Nette\Database\Table\ActiveRow;


class Newsletter extends BaseControl
{
	/** @var array */
	private $events;

	/** @var \Nette\Database\Table\ActiveRow */
	private $newsletter;

	/** @var NewsletterService */
	private $newsletterService;


	public function __construct(Translator $translator,
								NewsletterService $newsletterService,
								ActiveRow $newsletter,
								array $events)
	{
		parent::__construct($translator);
		$this->events = $events;
		$this->newsletter = $newsletter;
		$this->newsletterService = $newsletterService;
	}


	public function render()
	{
		$layout = $this->newsletter->newsletter_layout->layout;
		$content = $this->newsletter->newsletter_content->content;

		$html = $this->newsletterService->render($layout, $content);

		$this->createTemplate()->getLatte()
			->setLoader(new StringLoader())
			->render($html);
	}
}