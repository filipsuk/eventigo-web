<?php

namespace App\Modules\Core\Model;


class EventTagModel extends BaseModel
{
	const TABLE_NAME = 'events_tags';

	/**
	 * Returns event's tags joined in string ("#tag1 #tag2 #tag3")
	 * 
	 * @param int $eventId
	 * @return string
	 */
	public function getEventTagsString(int $eventId) : string
	{
		$hashtags = '';
		foreach ($this->getAll()->where('event_id', $eventId)->order('rate DESC')->fetchAll() as $eventTag) {
			$hashtags .= '#' . $eventTag->ref('tags', 'tag_id')->code . ' ';
		}
		return $hashtags;
	}
	
}
