<?php

namespace App\Modules\Core\Model;

use App\Modules\Front\Model\Exceptions\Subscription\EmailExistsException;
use CannotPerformOperationException;
use Nette\Database\Context;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\IRow;
use Nette\DI\Container;
use Nette\Security\Passwords;
use Nette\Utils\ArrayHash;
use Nette\Utils\Random;


class UserModel extends BaseModel
{
	const TABLE_NAME = 'users';

	/** Login types */
	const SUBSCRIPTION_LOGIN = 'subscription';
	const ADMIN_LOGIN = 'admin';

	const TOKEN_LENGTH = 64;

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
				// Create user
				/** @var ActiveRow $user */
				$user = $this->insert([
					'email' => $email,
				]);

				// Generate token for user
				$this->getAll()->wherePrimary($user->id)->update([
					'token' => $this->generateToken($user),
				]);

				return $user;
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


	public function generateToken() : string
	{
		do {
			$token = Random::generate(self::TOKEN_LENGTH);
		} while ($this->getAll()->where(['token' => $token])->fetch());

		return $token;
	}


	/**
	 * @param string $facebookId
	 * @return bool|IRow
	 */
	public function findByFacebookId($facebookId)
	{
		return $this->getAll()->where('facebook_id', $facebookId)->fetch();
	}


	/**
	 * @param ArrayHash $me
	 * @return IRow
	 */
	public function signInViaFacebook(ArrayHash $me)
	{
		if (isset($me->email) && $user = $this->getAll()->where('email', $me->email)->fetch()) {
			$this->getAll()->wherePrimary($user->id)->update([
				'facebook_id' => $me->id,
			]);
			return $this->findByFacebookId($me->id);
		} else {
			return $this->insert([
				'email' => $me->email ?? null,
				'facebook_id' => $me->id,
			]);
		}
	}


	public function updateFacebook(ArrayHash $me, string $token) : ActiveRow
	{
		$user = $this->getAll()->where('facebook_id', $me->id)->fetch();

		$this->getAll()->wherePrimary($user->id)->update([
			'facebook_token' => $token,
			'firstname' => $user->firstname ?: $me->first_name,
			'fullname' => $user->fullname ?: $me->name,
		]);

		return $this->findByFacebookId($me->id);
	}
}