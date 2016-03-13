<?php

namespace App\Modules\Admin\Model;

use App\Modules\Core\Model\UserModel;
use CannotPerformOperationException;
use CryptoTestFailedException;
use InvalidCiphertextException;
use Nette\DI\Container;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\Passwords;


class Authenticator implements IAuthenticator
{
	const INCORRECT_CREDENTIALS = 1;
	const USER_NOT_FOUND = 2;

	/** @var UserModel */
	private $userModel;

	/** @var Container */
	private $container;


	public function __construct(UserModel $userModel, Container $container)
	{
		$this->userModel = $userModel;
		$this->container = $container;
	}


	public function authenticate(array $credentials)
	{
		list($email, $password) = $credentials;

		// Get user with same email
		$user = $this->userModel->getAll()->where([
			'email' => $email,
		])->fetch();

		if ($user !== false) {
			$key = $this->container->getParameters()['security']['key'];

			try {
				$hash = \Crypto::Decrypt(base64_decode($user->password), $key);

				if (Passwords::verify($password, $hash)) {
					return new Identity($email);
				} else {
					throw new AuthenticationException('Incorrect credentials', Authenticator::INCORRECT_CREDENTIALS);
				}

			} catch (InvalidCiphertextException $e) {
				throw new AuthenticationException('Cannot decrypt ciphertext');
			} catch (CryptoTestFailedException $e) {
				throw new AuthenticationException('Cannot decrypt ciphertext');
			} catch (CannotPerformOperationException $e) {
				throw new AuthenticationException('Cannot decrypt ciphertext');
			}
		} else {
			throw new AuthenticationException('User with this email and password has not been found', Authenticator::USER_NOT_FOUND);
		}
	}
}