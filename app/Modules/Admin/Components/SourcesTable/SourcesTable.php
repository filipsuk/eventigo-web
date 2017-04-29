<?php declare(strict_types=1);

namespace App\Modules\Admin\Components\SourcesTable;

use App\Modules\Admin\Components\DataTable\DataTable;
use App\Modules\Admin\Model\SourceModel;
use App\Modules\Admin\Model\SourceService;
use App\Modules\Core\Model\EventSources\Utils\EventSource;
use App\Modules\Core\Model\EventSources\Facebook\FacebookEventSource;
use Kdyby\Translation\Translator;
use Nette\Database\Table\Selection;
use Nette\Utils\DateTime;
use Nette\Utils\Html;


class SourcesTable extends DataTable
{
	/**
	 * @var SourceModel
	 */
	private $sourceModel;

	/**
	 * @var SourceService
	 */
	private $sourceService;


	public function __construct(
        Translator $translator,
        Selection $dataSource,
        SourceModel $sourceModel,
		SourceService $sourceService
    ) {
		parent::__construct($translator, $dataSource);
		$this->sourceModel = $sourceModel;
		$this->sourceService = $sourceService;
	}


	/**
	 * @return array|\Nette\Database\Table\IRow[]|Selection
	 */
	public function getData()
	{
		return $this->dataSource
			->select('id, name, url, next_check')
			->order('next_check ASC')
			->order('name ASC')
			->fetchAll();
	}


	public function generateJson(): array
	{
		$json = [
			'aaData' => $this->getData(),
		];

		foreach ($json['aaData'] as &$item) {
			$item = $item->toArray();
			$isCrawlable = EventSource::isCrawlable($item['url']);

			$i = Html::el('i', ['class' => 'fa fa-external-link']);
			$name = $item['name'] . '&nbsp; '
				. (string) Html::el('a', [
					'href' => $item['url'],
					'target' => '_blank',
					'data-toggle' => 'tooltip',
					'title' => $item['url'],
				])->setHtml($i);
			if ($isCrawlable && FacebookEventSource::isSource($item['url'])) {
				$name = Html::el('i', ['class' => 'fa fa-facebook-square']) . '&nbsp;' . $name;
			}

			$item['name'] = $name;
			$item['nextCheck'] = DateTime::from($item['next_check'])
				->format(\App\Modules\Core\Utils\DateTime::W3C_DATE);

			$actions = (string) Html::el('a', [
				'href' => $this->link('crawl!', $item['id']),
				'class' => 'btn btn-primary btn-xs',
				'data-toggle' => 'tooltip',
				'title' => $this->translator->translate($isCrawlable
					? 'admin.sources.default.table.crawl.title'
					: 'admin.sources.default.table.done.title'),
			])->setHtml($isCrawlable
				? '<i class="fa fa-cloud-download"></i>'
				: '<i class="fa fa-check"></i>');
			$item['actions'] = $actions;
		}
		$json['aaData'] = array_values($json['aaData']);

		return $json;
	}


	public function handleCrawl(int $sourceId)
	{
		$source = $this->sourceModel->getAll()->wherePrimary($sourceId)->fetch();

		if (EventSource::isCrawlable($source->url)) {
			$addedEvents = $this->sourceService->crawlSource($source);

			if ($addedEvents > 0) {
				$this->getPresenter()->flashMessage($this->translator->translate('admin.events.crawlSources.success',
					$addedEvents, ['events' => $addedEvents]), 'success');
			} else {
				$this->getPresenter()->flashMessage($this->translator->translate('admin.events.crawlSources.noEvents'));
			}
		}

		$this->sourceModel->getAll()->wherePrimary($sourceId)->update([
			'next_check' => $nextCheck = new DateTime('+' . $source->check_frequency . ' days'),
		]);

		$this->getPresenter()->flashMessage($this->translator->translate('admin.sources.default.table.done', [
			'source' => $source->name,
			'nextCheck' => $nextCheck->format(\App\Modules\Core\Utils\DateTime::NO_ZERO_DATE_FORMAT),
		]));

		$this->getPresenter()->redirect('Sources:');
	}
}