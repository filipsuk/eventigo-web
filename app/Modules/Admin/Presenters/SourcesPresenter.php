<?php declare(strict_types=1);

namespace App\Modules\Admin\Presenters;

use App\Modules\Admin\Components\SourceForm\SourceFormFactory;
use App\Modules\Admin\Components\SourcesTable\SourcesTableFactory;
use App\Modules\Admin\Model\SourceModel;
use App\Modules\Admin\Model\SourceService;
use Nette\Database\Table\ActiveRow;
use Nette\Utils\DateTime;


class SourcesPresenter extends BasePresenter
{
	/** @var SourceFormFactory @inject  */
	public $sourceFormFactory;

	/** @var SourcesTableFactory @inject */
	public $sourcesTableFactory;

	/** @var SourceModel @inject */
	public $sourceModel;

	/** @var SourceService @inject */
	public $sourceService;


	public function actionCreate()
	{
		$defaults = [
			'frequencyNumber' => 1,
			'nextCheck' => (new DateTime('+1 days'))->format(\App\Modules\Core\Utils\DateTime::DATE_FORMAT),
			'createOrganiser' => true,
		];
		$this['sourceForm-form']->setDefaults($defaults);
	}


	protected function createComponentSourceForm()
	{
		$control = $this->sourceFormFactory->create();

		$control->onCreate[] = function (ActiveRow $source) {
			$this->flashMessage($this->translator->translate('admin.sourceForm.success'), 'success');

			// Crawl recently added source
			$addedEvents = $this->sourceService->crawlSource($source);
			if ($addedEvents > 0) {
				$this->flashMessage($this->translator->translate('admin.events.crawlSources.success',
					$addedEvents, ['events' => $addedEvents]), 'success');
			} else {
				$this->flashMessage($this->translator->translate('admin.events.crawlSources.noEvents'));
			}

			$this->redirect('Sources:default');
		};

		$control->onUpdate[] = function (ActiveRow $source) {
			$this->flashMessage($this->translator->translate('admin.sourceForm.success'), 'success');
			$this->redirect('Sources:update', ['id' => $source->id]);
		};

		return $control;
	}


	protected function createComponentSourcesTable()
	{
		return $this->sourcesTableFactory->create($this->sourceModel->getAll()->select('id, name, url'));
	}
}
