<?php declare(strict_types=1);

namespace App\Modules\Admin\Components\EventsTable;

use Nette\Database\Table\Selection;


interface NotApprovedEventsTableFactory
{
	public function create(Selection $dataSource): NotApprovedEventsTable;
}
