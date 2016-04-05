<?php

namespace App\Modules\Admin\Components\SourceForm;

use App\Modules\Admin\Model\SourceModel;
use App\Modules\Core\Components\BaseControl;
use App\Modules\Core\Components\Form\Form;
use App\Modules\Core\Utils\Collection;
use Kdyby\Translation\Translator;
use Nette\Utils\DateTime;


class SourceForm extends BaseControl
{
	/** @var array */
	public $onCreate = [];

	/** @var array */
	public $onUpdate = [];

	/** @var SourceModel */
	private $sourceModel;


	public function __construct(Translator $translator,
								SourceModel $sourceModel)
	{
		parent::__construct($translator);
		$this->sourceModel = $sourceModel;
	}


	public function render()
	{
		$this['form']->render();
	}


	public function createComponentForm()
	{
		$form = new Form;
		$form->setTranslator($this->translator->domain('admin.sourceForm'));

		// Event
		$form->addGroup();
		$form->addText('name', 'name')
			->setAttribute('autofocus', true);
		$form->addText('url', 'url')
			->addCondition(Form::FILLED)
			->addRule(Form::URL, 'url.wrong');
		$form->addSelect('frequency', 'frequency')
			->setItems(array_combine(
				$frequencyTypes = array_keys(SourceModel::FREQUENCY_TYPES),
				Collection::prefix($frequencyTypes, 'frequency.')
			));
		$form->addText('nextCheck', 'nextCheck')
			->setAttribute('class', 'date');

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
			$source = $this->sourceModel->insert([
				'name' => $values->name ?: null,
				'url' => $values->url ?: null,
				'organiser_id' => $values->organiserId ?? null,
				'event_series_id' => $values->organiserId ?? null,
				'check_frequency' => $checkFrequency = SourceModel::FREQUENCY_TYPES[$values->frequency],
				'next_check' => $values->nextCheck
					? DateTime::createFromFormat(\App\Modules\Core\Utils\DateTime::DATE_FORMAT, $values->nextCheck)
					: new DateTime('+' . $checkFrequency . ' days'),
			]);
			$this->onCreate($source);
		} else {
			$source = $this->sourceModel->update($values);
			$this->onUpdate($source);
		}
	}
}