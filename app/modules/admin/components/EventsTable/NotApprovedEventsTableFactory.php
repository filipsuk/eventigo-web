<?php

namespace App\Modules\Admin\Components\NotApprovedEventsTable;

use Nette\Database\Table\Selection;


interface NotApprovedEventsTableFactory
{
	/**
	 * @param Selection $dataSource
	 * @return NotApprovedEventsTable
	 */
	public function create(Selection $dataSource);
}