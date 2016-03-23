<?php

namespace App\Modules\Admin\Model;

use App\Modules\Core\Model\OrganiserModel;


class OrganiserService
{
	/** @var OrganiserModel */
	private $organiserModel;


	public function __construct(OrganiserModel $organiserModel)
	{
		$this->organiserModel = $organiserModel;
	}


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
				. ($item->series ? ': ' . $item->series : '');
		}

		return $result;
	}
}