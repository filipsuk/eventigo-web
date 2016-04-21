<?php

namespace App\Modules\Newsletter\Model;

use App\Modules\Core\Model\BaseModel;
use Nette\Utils\DateTime;
use Nette\Utils\Random;


class UserNewsletterModel extends BaseModel
{
	const TABLE_NAME = 'users_newsletters';


	/**
	 * @param int $userId
	 * @param string $content
	 * @return bool|int|\Nette\Database\Table\IRow
	 */
	public function createNewsletter($userId, $content)
	{
		return $this->insert([
			'user_id' => $userId,
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


	/**
	 * @param int[] $usersNewslettersIds
	 */
	public function sendNewsletters(array $usersNewslettersIds)
	{
		$usersNewsletters = $this->getAll()->wherePrimary($usersNewslettersIds)->fetchAll();
		foreach ($usersNewsletters as $userNewsletter) {
			$this->getAll()->wherePrimary($userNewsletter->id)->update([
				'sent' => new DateTime,
			]);

			// TODO Push newsletter to email queue
		}
	}
}