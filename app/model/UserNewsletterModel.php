<?php

namespace App\Model;

use Nette\Utils\DateTime;
use Nette\Utils\Json;
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


	public function sendNewsletters(array $usersNewslettersHashes)
	{
		$usersNewsletters = $this->getAll()->wherePrimary($usersNewslettersHashes)->fetchAll();
		foreach ($usersNewsletters as $userNewsletter) {
			// Get user tags
			$tags = [];
			$userTags = $userNewsletter->user->related('users_tags');
			foreach ($userTags as $userTag) {
				$tags[] = $userTag->tag->code;
			}

			// Save current user tags
			$this->update([
				'variables' => Json::encode($tags),
				'sent' => new DateTime,
			]);

			// TODO Push newsletter to email queue
		}
	}
}