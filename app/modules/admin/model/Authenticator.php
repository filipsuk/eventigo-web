<?php

namespace App\Modules\Admin\Model;

use App\Modules\Core\Model\UserModel;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;


class Authenticator implements IAuthenticator
{
	/** @var UserModel */
	private $userModel;


	public function __construct(UserModel $userModel)
	{
		$this->userModel = $userModel;
	}


	public function authenticate(array $credentials)
	{
		$user = $this->userModel->getAll()->where([
			'email' => $credentials[0],
			'password' => $credentials[1], // TODO bcrypt
		])->fetch();

		if ($user !== false) {
			return new Identity($credentials[0]);
		} else {
			throw new AuthenticationException('User with this email and password has not been found');
		}
	}
}