<?php

namespace App\Modules\Admin\Components\SourcesTable;

use App\Modules\Admin\Components\DataTable\DataTable;
use App\Modules\Admin\Model\SourceModel;
use App\Modules\Core\Model\EventSources\Facebook\FacebookEventSource;
use Kdyby\Translation\Translator;
use Nette\Database\Table\Selection;
use Nette\Utils\DateTime;
use Nette\Utils\Html;


class SourcesTable extends DataTable
{
	/** @var SourceModel */
	private $sourceModel;


	public function __construct(Translator $translator, Selection $dataSource, SourceModel $sourceModel)
	{
		parent::__construct($translator, $dataSource);
		$this->sourceModel = $sourceModel;
	}


	public function getData()
	{
		return $this->dataSource
			->select('id, name, url, next_check')
			->order('next_check ASC')
			->order('name ASC')
			->fetchAll();
	}


	public function generateJson()
	{
		$json = [
			'aaData' => $this->getData(),
		];

		foreach ($json['aaData'] as &$item) {
			$item = $item->toArray();

			$i = Html::el('i', ['class' => 'fa fa-external-link']);
			$name = $item['name'] . '&nbsp; '
				. (string)Html::el('a', [
					'href' => $item['url'],
					'target' => '_blank',
					'data-toggle' => 'tooltip',
					'title' => $item['url'],
				])->setHtml($i);
			if (FacebookEventSource::isFacebook($item['url'])) {
				$name = Html::el('i', ['class' => 'fa fa-facebook-square']) . '&nbsp;' . $name;
			}

			$item['name'] = $name;
			$item['nextCheck'] = DateTime::from($item['next_check'])
				->format(\App\Modules\Core\Utils\DateTime::W3C_DATE);

			$actions = (string)Html::el('a', [
				'href' => $this->link('done!', $item['id']),
				'class' => 'btn btn-primary btn-xs',
				'data-toggle' => 'tooltip',
				'title' => $this->translator->translate('admin.sources.default.table.done.title'),
			])->setHtml('<i class="fa fa-check"></i>');
			$item['actions'] = $actions;
		}
		$json['aaData'] = array_values($json['aaData']);

		return $json;
	}


	public function handleDone(int $sourceId)
	{
		$source = $this->sourceModel->getAll()->wherePrimary($sourceId)->fetch();
		$this->sourceModel->getAll()->wherePrimary($sourceId)->update([
			'next_check' => $nextCheck = new DateTime('+' . $source->check_frequency . ' days'),
		]);

		$this->getPresenter()->flashMessage($this->translator->translate('admin.sources.default.table.done', [
			'source' => $source->name,
			'nextCheck' => $nextCheck->format(\App\Modules\Core\Utils\DateTime::NO_ZERO_DATE_FORMAT),
		]), 'success');

		$this->getPresenter()->redirect('Sources:');
	}
}