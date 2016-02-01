<?php

namespace App\Model;

use App\Model\Exceptions\Subscription\EmailExistsException;
use Nette\Database\Table\IRow;


class UserModel extends BaseModel
{
	const TABLE_NAME = 'users';


	/**
	 * @param string $email
	 * @return IRow|null
	 * @throws EmailExistsException
	 * @throws \PDOException
	 */
	public function subscribe(string $email)
	{
		if ($email) {
			if ($this->emailExists($email)) {
				throw new EmailExistsException;

			} else {
				return $this->insert([
					'email' => $email,
				]);
			}

		} else {
			return NULL;
		}
	}


	public function emailExists(string $email) : bool
	{
		return (bool)$this->getAll()
			->where('email', $email)
			->fetch();
	}
}