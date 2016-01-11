<?php

namespace App\Model;


class UserModel extends BaseModel
{
	const TABLE_NAME = 'users';


	public function emailExists(string $email) : bool
	{
		return (bool)$this->getAll()
			->where('email', $email)
			->fetch();
	}
}