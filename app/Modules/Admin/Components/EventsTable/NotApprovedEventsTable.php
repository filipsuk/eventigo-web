<?php declare(strict_types=1);

namespace App\Modules\Admin\Components\EventsTable;

use App\Modules\Admin\Components\DataTable\AbstractDataTable;
use App\Modules\Core\Model\EventModel;
use App\Modules\Core\Model\EventSources\Facebook\FacebookEventSource;
use Kdyby\Translation\Translator;
use Nette\Database\Table\IRow;
use Nette\Database\Table\Selection;
use Nette\Utils\DateTime;
use Nette\Utils\Html;


final class NotApprovedEventsTable extends AbstractDataTable
{
	/**
	 * @var EventModel
	 */
	private $eventModel;


	public function __construct(Translator $translator, Selection $dataSource, EventModel $eventModel)
    {
		parent::__construct($translator, $dataSource);
		$this->eventModel = $eventModel;
	}


	/**
	 * @return array|IRow[]|Selection
	 */
	public function getData()
	{
		return $this->dataSource
			->select('events.id, events.name, events.origin_url, events.start, events.end, events.created,
				event_series.name AS event_series, event_series.organiser.name AS organiser')
			->order('created ASC')
			->order('start ASC')
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

			$item['series'] = $item['organiser']
				? $item['organiser']
					. ($item['organiser'] !== $item['event_series'] ? ': ' . $item['event_series'] : '')
				: '';

			$i = Html::el('i', ['class' => 'fa fa-external-link']);
			$name = $item['name'] . '&nbsp; '
				. (string) Html::el('a', [
					'href' => $item['origin_url'],
					'target' => '_blank',
					'data-toggle' => 'tooltip',
					'title' => $item['origin_url'],
				])->setHtml($i);
			if (FacebookEventSource::isSource($item['origin_url'])) {
				$name = Html::el('i', ['class' => 'fa fa-facebook-square']) . '&nbsp;' . $name;
			}
			$item['name'] = (string) Html::el('a', [
				'href' => $this->getPresenter()->link('Events:approve', $item['id']),
				'data-toggle' => 'tooltip',
				'title' => $this->translator->translate('admin.notApprovedEventsTable.approve.title'),
			])->setHtml($name);

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

			$actions = (string) Html::el('a', [
				'href' => $this->getPresenter()->link('Events:approve', $item['id']),
				'class' => 'btn btn-primary btn-xs',
				'data-toggle' => 'tooltip',
				'title' => $this->translator->translate('admin.notApprovedEventsTable.approve.title'),
			])->setHtml('<i class="fa fa-pencil"></i>');
			$actions .= (string) Html::el('a', [
				'href' => $this->link('skip!', $item['id']),
				'class' => 'btn btn-default btn-xs',
				'data-toggle' => 'tooltip',
				'title' => $this->translator->translate('admin.notApprovedEventsTable.skip.title'),
			])->setHtml('<i class="fa fa-times"></i>');
			$item['actions'] = $actions;
		}
		$json['aaData'] = array_values($json['aaData']);

		return $json;
	}


	public function handleSkip(int $eventId)
	{
		$event = $this->eventModel->getAll()->wherePrimary($eventId)->fetch();
		$this->eventModel->getAll()->wherePrimary($eventId)->update([
			'state' => EventModel::STATE_SKIP,
		]);

		$this->getPresenter()->flashMessage($this->translator->translate('admin.notApprovedEventsTable.skip.success',
			['name' => $event->name]));
		$this->getPresenter()->redirect('this');
	}


	public function renderJs()
	{
		$this->template->setFile(__DIR__ . '/js.latte');
		$this->template->render();
	}
}
