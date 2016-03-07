<?php

namespace App\Modules\Newsletter\Components\Newsletter;

use App\Modules\Core\Components\BaseControl;
use App\Modules\Front\Components\EventsList\EventsListFactory;
use Kdyby\Translation\Translator;
use Latte\Loaders\StringLoader;
use Nette\Database\Table\ActiveRow;


class Newsletter extends BaseControl
{
	/** @var array */
	private $events;

	/** @var EventsListFactory */
	private $eventsListFactory;

	/** @var \Nette\Database\Table\ActiveRow */
	private $newsletter;


	public function __construct(Translator $translator,
								EventsListFactory $eventsListFactory,
								ActiveRow $newsletter,
								array $events)
	{
		parent::__construct($translator);
		$this->events = $events;
		$this->eventsListFactory = $eventsListFactory;
		$this->newsletter = $newsletter;
	}


	public function render()
	{
		$layout = $this->newsletter->newsletter_layout->layout;
		$content = $this->newsletter->newsletter_content->content;

		$contentTemplate = $this->createTemplate();
		$contentString = $contentTemplate->getLatte()
			->setLoader(new StringLoader())
			->renderToString($content, ['_control' => $this]);

		$template = $this->createTemplate();
		$template->getLatte()
			->setLoader(new StringLoader())
			->render($layout, ['content' => $contentString]);
	}


	public function createComponentEventsList()
	{
		return $this->eventsListFactory->create($this->events);
	}
}