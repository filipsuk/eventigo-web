<?php

namespace App\Modules\Admin\Components\SourcesTable;

use App\Modules\Admin\Components\DataTable\DataTable;
use Nette\Utils\DateTime;
use Nette\Utils\Html;


class SourcesTable extends DataTable
{
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
			$item['name'] = (string)Html::el('a', ['href' => $item['url'], 'target' => '_blank'])
				->setHtml($item['name'] . '&nbsp; ' . (string)$i);
			$item['nextCheck'] = DateTime::from($item['next_check'])
				->format(\App\Modules\Core\Utils\DateTime::DATE_FORMAT);
		}
		$json['aaData'] = array_values($json['aaData']);

		return $json;
	}
}