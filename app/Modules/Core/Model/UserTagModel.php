<?php declare(strict_types=1);

namespace App\Modules\Core\Model;

class UserTagModel extends BaseModel
{
	/**
	 * @var string
	 */
	const TABLE_NAME = 'users_tags';


	public function getUsersTags(int $userId): array
	{
		return $this->getAll()
			->select('tag.tag_group.name AS tagGroupName')
			->select('tag.code')
			->where('user_id', $userId)
			->group('tag.tag_group_id, tag.id')
			->fetchAssoc('tagGroupName[]=code');
	}

	/**
	 * Get ids of user's subscribed tags
	 */
	public function getUserTagIds(int $userId): array
	{
		return $this->getAll()
			->select('tag_id')
			->where('user_id', $userId)
			->fetchAssoc('[]=tag_id');
	}
}
