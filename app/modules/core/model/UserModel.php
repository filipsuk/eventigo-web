<?php

namespace App\Modules\Core\Model;

use App\Modules\Front\Model\Exceptions\Subscription\EmailExistsException;
use CannotPerformOperationException;
use Nette\Database\Context;
use Nette\Database\Table\IRow;
use Nette\DI\Container;
use Nette\Security\Passwords;


class UserModel extends BaseModel
{
	const TABLE_NAME = 'users';

	/** @var Container */
	private $container;


	public function __construct(Context $database, Container $container)
	{
		parent::__construct($database);
		$this->container = $container;
	}


	/**
	 * @param string $email
	 * @return IRow|null
	 * @throws EmailExistsException
	 * @throws \PDOException
	 */
	public function subscribe($email)
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


	/**
	 * @param string $email
	 * @return bool
	 */
	public function emailExists($email)
	{
		return (bool)$this->getAll()
			->where('email', $email)
			->fetch();
	}


	/**
	 * Hash and encrypt the password
	 * @param string $password
	 * @return string
	 * @throws CannotPerformOperationException
	 */
	public function hashAndEncrypt($password)
	{
		$key = $this->container->getParameters()['security']['key'];
		return base64_encode(\Crypto::Encrypt(Passwords::hash($password), $key));
	}
}