<?php

namespace App\Modules\Core\Model;


class UserTagModel extends BaseModel
{
	const TABLE_NAME = 'users_tags';


	public function getUsersTags(int $userId) : array
	{
		return $this->getAll()
			->select('tag.tag_group.name AS tagGroupName')
			->select('tag.code')
			->where('user_id', $userId)
			->group('tag.tag_group_id, tag.id')
			->fetchAssoc('tagGroupName[]=code');
	}
}