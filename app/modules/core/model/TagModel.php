<?php

namespace App\Modules\Core\Model;


class TagModel extends BaseModel
{
	const TABLE_NAME = 'tags';


	public function getAllByMostEvents()
	{
		return $this->getAll()
			->select('tags.code')
			->select('tags.name')
			->select('COUNT(:events_tags.event_id) AS eventsCount')
			->where(':events_tags.event.start >= NOW()')
			->group('tags.id')
			->order('eventsCount DESC')
			->order('tags.name');
	}
}