<?php declare(strict_types=1);

namespace App\Modules\Newsletter\Model;

use App\Modules\Core\Model\AbstractBaseModel;
use Nette\Utils\Random;

final class UserNewsletterModel extends AbstractBaseModel
{
	/**
	 * @var string
	 */
	const TABLE_NAME = 'users_newsletters';

	/**
	 * @return bool|int|\Nette\Database\Table\IRow
	 */
	public function createNewsletter(int $userId, string $from, string $subject, string $content, ?string $hash = null)
	{
		return $this->insert([
			'user_id' => $userId,
			'from' => $from,
			'subject' => $subject,
			'content' => $content,
			'hash' => $hash ?? $this->generateUniqueHash(),
		]);
	}


	public function generateUniqueHash(): string
	{
		do {
			$hash = Random::generate(32);
		} while ($this->getAll()->where(['hash' => $hash])->fetch());

		return $hash;
	}
}
