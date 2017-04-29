<?php declare(strict_types=1);

namespace App\Modules\Front\Presenters;

use App\Modules\Core\Model\TagModel;
use App\Modules\Core\Model\UserModel;
use App\Modules\Core\Model\UserTagModel;
use App\Modules\Core\Presenters\AbstractBasePresenter as CoreAbstractBasePresenter;
use App\Modules\Core\Utils\DateTime;
use Nette\Utils\DateTime as NetteDateTime;

abstract class AbstractBasePresenter extends CoreAbstractBasePresenter
{
	/**
	 * @var UserModel @inject
	 */
	public $userModel;

	/**
	 * @var UserTagModel @inject
	 */
	public $userTagModel;

	/**
	 * @var TagModel @inject
	 */
	public $tagModel;

	/**
	 * @var NetteDateTime
	 */
	protected $lastAccess;


	protected function startup(): void
	{
		parent::startup();

		$this->updateAccess();
	}


	private function updateAccess(): void
	{
		$access = $this->getSession('access');
		$now = new NetteDateTime;

		// Get last access stored in DB
		if ($this->getUser()->isLoggedIn()) {
			$user = $this->userModel->getAll()
				->wherePrimary($this->getUser()->getId())
				->fetch();
			$lastInDb = $user->last_access;
		}

		// Store the newest access
		$this->lastAccess = DateTime::max($access->last ?? null, $lastInDb ?? null);

		// Update last access
		$access->last = clone $now;

		// Update last access in DB if it has been updated few minutes ago or earlier
		$syncToDb = $this->context->getParameters()['lastAccess']['syncToDb'] ?? '5 minutes';
		if ($this->getUser()->isLoggedIn()
			&& $this->shouldSyncToDb($access->lastInDb ?? null, $lastInDb ?? null, $syncToDb)
		) {
			$this->userModel->getAll()
				->wherePrimary($this->getUser()->getId())
				->update([
					'last_access' => $now,
				]);

			// Update last in DB access
			$access->lastInDb = clone $now;
		}
	}


	private function shouldSyncToDb(
		?NetteDateTime $sessionLastInDb = null, ?NetteDateTime $lastInDb = null, string $syncToDb
	): bool {
		return ! $sessionLastInDb
		|| ($lastInDb && $lastInDb > $sessionLastInDb)
		|| $sessionLastInDb < new NetteDateTime('-' . $syncToDb);
	}
}
