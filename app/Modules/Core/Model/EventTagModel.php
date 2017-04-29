<?php declare(strict_types=1);

namespace App\Modules\Core\Model;

final class EventTagModel extends AbstractBaseModel
{
	/**
	 * @var string
	 */
	protected const TABLE_NAME = 'events_tags';

	/**
	 * Returns event's tags joined in string ("#tag1 #tag2 #tag3").
	 */
	public function getEventTagsString(int $eventId): string
	{
		$hashtags = '';
		foreach ($this->getAll()->where('event_id', $eventId)->order('rate DESC')->fetchAll() as $eventTag) {
			$hashtags .= '#' . $eventTag->ref('tags', 'tag_id')->code . ' ';
		}

		return $hashtags;
	}
}
