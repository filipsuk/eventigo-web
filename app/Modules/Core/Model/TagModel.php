<?php declare(strict_types=1);

namespace App\Modules\Core\Model;

use Nette\Database\Table\Selection;

final class TagModel extends AbstractBaseModel
{
	/**
	 * @var string
	 */
	const TABLE_NAME = 'tags';


	/**
	 * Get tags with the most upcoming events
	 */
	public function getByMostEvents(): Selection
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


	/**
	 * Get tags with the most upcoming events and all others
	 */
	public function getAllByMostEvents(): Selection
	{
		return $this->getAll()
			->select('tags.code')
			->select('tags.name')
			->select('COUNT(IF(:events_tags.event_id IS NOT NULL AND :events_tags.event.start >= NOW(),'
				.' TRUE, NULL)) AS eventsCount')
			->select('tag_group_id')
			->select('tag_group.name AS tagGroupName')
			->select('tags.id')
			->group('tag_group_id, tags.id')
			->order('tag_group_id')
			->order('eventsCount DESC')
			->order('tags.name');
	}
}
