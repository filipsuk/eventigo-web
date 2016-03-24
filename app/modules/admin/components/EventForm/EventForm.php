<?php

namespace App\Modules\Admin\Components\EventForm;

use App\Modules\Admin\Model\EventService;
use App\Modules\Core\Components\BaseControl;
use App\Modules\Core\Components\Form\Form;
use App\Modules\Core\Model\TagModel;
use Kdyby\Translation\Translator;


class EventForm extends BaseControl
{
	/** @var TagModel */
	private $tagModel;

	/** @var EventService */
	private $eventService;

	/** @var array */
	public $onCreate = [];

	/** @var array */
	public $onUpdate = [];


	public function __construct(Translator $translator,
	                            TagModel $tagModel,
	                            EventService $eventService)
	{
		parent::__construct($translator);
		$this->tagModel = $tagModel;
		$this->eventService = $eventService;
	}

	public function render()
	{
		$this['form']->render();
	}


	public function createComponentForm()
	{
		$form = new Form;
		$form->setTranslator($this->translator->domain('admin.eventForm'));

		// Event
		$form->addGroup();
		$form->addText('name', 'name')
			->setRequired('name.required')
			->setAttribute('autofocus', true);
		$form->addText('start', 'start')
			->setRequired('start.required')
			->setAttribute('class', 'datetime');
		$form->addText('end', 'end')
			->setAttribute('class', 'datetime');
		$form->addText('origin_url', 'originUrl')
			->addCondition(Form::FILLED)
			->addRule(Form::URL, 'originUrl.wrong');
		$form->addTextArea('description', 'description')
			->setRequired('description.required');
		$form->addText('image', 'image')
			->addCondition(Form::FILLED)
			->addRule(Form::URL, 'image.wrong');
		$form->addSelect('rate', $this->translator->translate('admin.eventForm.rate'), $this->getEventRates())
			->setPrompt($this->translator->translate('admin.eventForm.rate.prompt'))
			->setRequired('rate.required')
			->setTranslator(null);

		// Tags
		$form->addGroup();
		$tags = $this->tagModel->getAll()->fetchPairs('code', 'name');
		$tagsContainer = $form->addContainer('tags');
		for ($i = 0; $i < 5; $i++) {
			$tagContainer = $tagsContainer->addContainer($i);

			$codeControl = $tagContainer->addSelect('code', $this->translator->translate('admin.eventForm.tag'), $tags)
				->setPrompt($this->translator->translate('admin.eventForm.tag.prompt'))
				->setTranslator(null);

			$rateControl = $tagContainer->addSelect('rate', $this->translator->translate('admin.eventForm.tag.rate'), $this->getTagsRates())
				->setPrompt($this->translator->translate('admin.eventForm.tag.rate.prompt'))
				->setTranslator(null);
			$rateControl->addConditionOn($codeControl, Form::FILLED)
				->setRequired($this->translator->translate('tag.rate.required'));
		}

		$form->addHidden('id');

		$form->addSubmit('save', 'save')
			->setAttribute('class', 'btn btn-success');
		$form->onSubmit[] = [$this, 'processForm'];

		return $form;
	}


	public function processForm(Form $form)
	{
		$values = $form->getValues();
		if (!$values->id) {
			$this->eventService->createEvent($values);
			$this->onCreate();
		} else {
			$this->eventService->updateEvent($values);
			$this->onUpdate();
		}
	}


	private function getEventRates()
	{
		return [
			1 => $this->translator->translate('admin.eventForm.rate.meetup'),
			$this->translator->translate('admin.eventForm.rate.bigMeetup'),
			$this->translator->translate('admin.eventForm.rate.conference'),
			$this->translator->translate('admin.eventForm.rate.bigConference'),
			$this->translator->translate('admin.eventForm.rate.extraConference'),
		];
	}


	private function getTagsRates()
	{
		return [
			1 => $this->translator->translate('admin.eventForm.tag.rate.little'),
			$this->translator->translate('admin.eventForm.tag.rate.more'),
			$this->translator->translate('admin.eventForm.tag.rate.good'),
			$this->translator->translate('admin.eventForm.tag.rate.reallyGood'),
			$this->translator->translate('admin.eventForm.tag.rate.total'),
		];
	}
}