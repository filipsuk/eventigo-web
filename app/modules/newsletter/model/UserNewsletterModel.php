<?php

namespace App\Modules\Newsletter\Model;

use App\Modules\Core\Model\BaseModel;
use Nette\Utils\Random;


class UserNewsletterModel extends BaseModel
{
	const TABLE_NAME = 'users_newsletters';

	/**
	 * @param int $userId
	 * @param string $from
	 * @param string $subject
	 * @param string $content
	 * @return bool|int|\Nette\Database\Table\IRow
	 */
	public function createNewsletter($userId, $from, $subject, $content)
	{
		return $this->insert([
			'user_id' => $userId,
			'from' => $from,
			'subject' => $subject,
			'content' => $content,
			'hash' => $this->generateUniqueHash(),
		]);
	}


	public function generateUniqueHash() : string
	{
		do {
			$hash = Random::generate(32);
		} while ($this->getAll()->where(['hash' => $hash])->fetch());

		return $hash;
	}
}