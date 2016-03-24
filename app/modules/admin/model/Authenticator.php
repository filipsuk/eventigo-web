<?php

namespace App\Modules\Admin\Model;

use App\Modules\Core\Model\UserModel;
use CannotPerformOperationException;
use CryptoTestFailedException;
use InvalidCiphertextException;
use Nette\Database\Table\ActiveRow;
use Nette\DI\Container;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\Passwords;


class Authenticator implements IAuthenticator
{
	/** @var UserModel */
	private $userModel;

	/** @var Container */
	private $container;


	public function __construct(UserModel $userModel, Container $container)
	{
		$this->userModel = $userModel;
		$this->container = $container;
	}


	/**
	 * @param array $credentials
	 * @return Identity
	 * @throws AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		@list($type, $email, $password) = $credentials; // Password is optional, suppress the notice

		// Get user with same email
		$user = $this->userModel->getAll()->where([
			'email' => $email,
		])->fetch();

		if ($user !== false) {
			switch ($type) {
				case UserModel::SUBSCRIPTION_LOGIN:
					return new Identity($email);
				case UserModel::ADMIN_LOGIN:
					return $this->logToAdmin($user, $password);
				default:
					throw new AuthenticationException('Unsupported login type', IAuthenticator::FAILURE);
			}
		} else {
			throw new AuthenticationException('User with this email and password has not been found', IAuthenticator::IDENTITY_NOT_FOUND);
		}
	}


	/**
	 * @param ActiveRow $user
	 * @param string $password
	 * @return Identity
	 * @throws AuthenticationException
	 */
	private function logToAdmin(ActiveRow $user, string $password)
	{
		$key = $this->container->getParameters()['security']['key'];

		try {
			$hash = \Crypto::Decrypt(base64_decode($user->password), $key);

			if (Passwords::verify($password, $hash)) {
				return new Identity($user->email);
			} else {
				throw new AuthenticationException('Incorrect credentials', IAuthenticator::INVALID_CREDENTIAL);
			}

		} catch (InvalidCiphertextException $e) {
			throw new AuthenticationException('Cannot decrypt ciphertext');
		} catch (CryptoTestFailedException $e) {
			throw new AuthenticationException('Cannot decrypt ciphertext');
		} catch (CannotPerformOperationException $e) {
			throw new AuthenticationException('Cannot decrypt ciphertext');
		}
	}
}