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
			->select('tag_group_id')
			->select('tag_group.name AS tagGroupName')
			->select('tags.id')
			->where(':events_tags.event.start >= NOW()')
			->group('tag_group_id, tags.id')
			->order('tag_group_id')
			->order('eventsCount DESC')
			->order('tags.name');
	}
}