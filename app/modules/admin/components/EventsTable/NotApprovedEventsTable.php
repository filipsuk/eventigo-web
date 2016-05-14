<?php

namespace App\Modules\Admin\Components\NotApprovedEventsTable;

use App\Modules\Admin\Components\DataTable\DataTable;
use App\Modules\Admin\Model\SourceModel;
use App\Modules\Core\Model\EventSources\Facebook\FacebookEventSource;
use Kdyby\Translation\Translator;
use Nette\Database\Table\Selection;
use Nette\Utils\DateTime;
use Nette\Utils\Html;


class NotApprovedEventsTable extends DataTable
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
			->select('id, name, origin_url, start, end, created')
			->order('created ASC')
			->order('start ASC')
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
			$name = $item['name'] . '&nbsp; ' . (string)$i;
			if (FacebookEventSource::isFacebook($item['origin_url'])) {
				$name = Html::el('i', ['class' => 'fa fa-facebook-square']) . '&nbsp;' . $name;
			}
			$item['name'] = (string)Html::el('a', ['href' => $item['origin_url'], 'target' => '_blank'])->setHtml($name);

			$start = DateTime::from($item['start']);
			$end = DateTime::from($item['end']);
			$item['date'] = $start->format(\App\Modules\Core\Utils\DateTime::W3C_DATETIME_MINUTES)
				. ($item['end'] && $item['start'] !== $item['end']
					? ' - ' . ($start->format('Y-m-d') === $end->format('Y-m-d')
						? $end->format(\App\Modules\Core\Utils\DateTime::TIME_MINUTES)
						: $end->format(\App\Modules\Core\Utils\DateTime::W3C_DATETIME_MINUTES))
					: '');

			$item['created'] = DateTime::from($item['created'])
				->format(\App\Modules\Core\Utils\DateTime::W3C_DATE);

			$actions = (string)Html::el('a', [
				'href' => $this->getPresenter()->link('Events:approve', $item['id']),
				'class' => 'btn btn-primary btn-sm',
				'data-toggle' => 'tooltip',
				'title' => $this->translator->translate('admin.notApprovedEventsTable.approve.title'),
			])->setHtml('<i class="fa fa-pencil"></i>');
			$item['actions'] = $actions;
		}
		$json['aaData'] = array_values($json['aaData']);

		return $json;
	}


	public function renderJs()
	{
		$this->template->setFile(__DIR__ . '/js.latte');
		$this->template->render();
	}
}