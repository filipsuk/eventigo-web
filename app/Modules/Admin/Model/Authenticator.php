<?php declare(strict_types=1);

namespace App\Modules\Admin\Model;

use App\Modules\Core\Model\UserModel;
use Nette\Database\Table\ActiveRow;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\Passwords;

final class Authenticator implements IAuthenticator
{
    /**
     * @var UserModel
     */
    private $userModel;

    public function __construct(UserModel $userModel)
    {
        $this->userModel = $userModel;
    }

    /**
     * @param string[] $credentials
     *
     * @throws AuthenticationException
     */
    public function authenticate(array $credentials): Identity
    {
        @list($type, $email, $password) = $credentials; // Password is optional, suppress the notice

        // Get user with same email
        $user = $this->userModel->getAll()->where([
            'email' => $email,
        ])->fetch();

        if ($user !== false) {
            switch ($type) {
                case UserModel::SUBSCRIPTION_LOGIN:
                    return new Identity($user->id, null, $user->toArray());
                case UserModel::ADMIN_LOGIN:
                    return $this->logToAdmin($user, $password);
                default:
                    throw new AuthenticationException('Unsupported login type', IAuthenticator::FAILURE);
            }
        } else {
            throw new AuthenticationException(
                'User with this email and password has not been found',
                IAuthenticator::IDENTITY_NOT_FOUND
            );
        }
    }

    private function logToAdmin(ActiveRow $user, string $password): Identity
    {
        if (Passwords::verify($password, $user['password'])) {
            return new Identity($user['id'], ['admin'], $user->toArray());
        }

        throw new AuthenticationException('Incorrect credentials', IAuthenticator::INVALID_CREDENTIAL);
    }
}
