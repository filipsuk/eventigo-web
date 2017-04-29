<?php declare(strict_types=1);

namespace App\Modules\Admin\Components\SourceForm;

use App\Modules\Admin\Model\OrganiserService;
use App\Modules\Admin\Model\SourceModel;
use App\Modules\Core\Components\AbstractBaseControl;
use App\Modules\Core\Components\Form\Form;
use App\Modules\Core\Utils\Collection;
use App\Modules\Core\Utils\DateTime;
use Nette\Utils\DateTime as NetteDateTime;

final class SourceForm extends AbstractBaseControl
{
	/**
	 * @var callable[]
	 */
	public $onCreate = [];

	/**
	 * @var callable[]
	 */
	public $onUpdate = [];

	/**
	 * @var SourceModel @inject
	 */
	public $sourceModel;

	/**
	 * @var OrganiserService @inject
	 */
	public $organiserService;

	public function render(): void
	{
		$this['form']->render();
	}

	public function processForm(Form $form): void
	{
		$values = $form->getValues();

		if (! $values->id) {
			if ($values->createOrganiser) {
				$organiser = $this->organiserService->createOrganiser($values->name, $values->url);
			}

			$source = $this->sourceModel->insert([
				'name' => $values->name ?: null,
				'url' => $values->url ?: null,
				'check_frequency' => $checkFrequency = SourceModel::FREQUENCY_TYPES[$values->frequency],
				'next_check' => $values->nextCheck
					? NetteDateTime::createFromFormat(DateTime::DATE_FORMAT, $values->nextCheck)
					: new NetteDateTime('+' . $checkFrequency . ' days'),
				'event_series_id' => $values->createOrganiser
					? $organiser->related('events_series')->fetch()->id
					: null,
			]);

			$this->onCreate($source);
		} else {
			$source = $this->sourceModel->update($values);
			$this->onUpdate($source);
		}
	}

	protected function createComponentForm(): Form
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

		$form->addCheckbox('createOrganiser', 'createOrganiser');

		$form->addHidden('id');

		$form->addSubmit('save', 'save')
			->setAttribute('class', 'btn btn-success');
		$form->onSubmit[] = [$this, 'processForm'];

		return $form;
	}
}
