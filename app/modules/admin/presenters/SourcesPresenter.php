<?php

namespace App\Modules\Admin\Presenters;

use App\Modules\Admin\Components\SourceForm\SourceFormFactory;
use App\Modules\Admin\Model\SourceModel;
use Nette\Database\Table\ActiveRow;
use Nette\Utils\DateTime;


class SourcesPresenter extends BasePresenter
{
	/** @var SourceFormFactory @inject  */
	public $sourceFormFactory;

	/** @var SourceModel @inject */
	public $sourceModel;


	public function actionCreate($id)
	{
		$defaults = [
			'frequencyNumber' => 1,
			'nextCheck' => (new DateTime('+1 days'))->format(\App\Modules\Core\Utils\DateTime::DATE_FORMAT),
		];
		$this['sourceForm-form']->setDefaults($defaults);
	}


	public function createComponentSourceForm()
	{
		$control = $this->sourceFormFactory->create();

		$control->onCreate[] = function(ActiveRow $source) {
			$this->flashMessage($this->translator->translate('admin.sourceForm.success'), 'success');
			$this->redirect('Sources:default');
		};

		$control->onUpdate[] = function(ActiveRow $source) {
			$this->flashMessage($this->translator->translate('admin.sourceForm.success'), 'success');
			$this->redirect('Sources:update', ['id' => $source->id]);
		};

		return $control;
	}
}
