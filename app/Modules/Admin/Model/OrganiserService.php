<?php declare(strict_types=1);

namespace App\Modules\Admin\Model;

use App\Modules\Core\Model\EventSeriesModel;
use App\Modules\Core\Model\OrganiserModel;
use Nette\Database\Table\ActiveRow;


class OrganiserService
{
	/** @var OrganiserModel @inject */
	public $organiserModel;

	/** @var EventSeriesModel @inject */
	public $eventSeriesModel;


	/**
	 * @return array|\Nette\Database\Table\IRow[]
	 */
	public function getOrganisersSeries()
	{
		return $this->organiserModel->getAll()
			->select('organisers.name AS organiser')
			->select(':events_series.name AS series')
			->select(':events_series.id AS seriesId')
			->fetchAll();
	}


	/**
	 * @param array|\Nette\Database\Table\IRow[] $series
	 * @return array|\Nette\Database\Table\IRow[]
	 */
	public static function formatSeriesForSelect(array $series)
	{
		$result = [];

		foreach ($series as $item) {
			$result[$item->seriesId] = $item->organiser
				. ($item->series && $item->organiser !== $item->series
					? ': ' . $item->series
					: '');
		}

		return $result;
	}


	public function createOrganiser(string $name, string $url): ActiveRow
	{
		$organiser = $this->organiserModel->insert([
			'name' => $name,
		]);

		$this->eventSeriesModel->insert([
			'organiser_id' => $organiser->id,
			'name' => $name,
			'url' => $url,
		]);

		return $organiser;
	}
}