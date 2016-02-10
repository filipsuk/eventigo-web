<?php

namespace App\Model;

use Nette\Utils\Random;


class UserNewsletterModel extends BaseModel
{
	const TABLE_NAME = 'users_newsletters';


	public function createNewsletter(int $userId, int $newsletterId)
	{
		return $this->insert([
			'user_id' => $userId,
			'newsletter_id' => $newsletterId,
			'hash' => $this->generateUniqueHash(),
		]);
	}


	private function generateUniqueHash() : string
	{
		do {
			$hash = Random::generate(32);
		} while ($this->getAll()->where(['hash' => $hash])->fetch());

		return $hash;
	}
}