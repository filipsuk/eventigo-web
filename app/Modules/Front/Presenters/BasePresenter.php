<?php declare(strict_types=1);

namespace App\Modules\Front\Presenters;

use App\Modules\Core\Model\TagModel;
use App\Modules\Core\Model\UserModel;
use App\Modules\Core\Model\UserTagModel;
use App\Modules\Core\Utils\Collection;
use Nette;
use Nette\Utils\DateTime;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends \App\Modules\Core\Presenters\BasePresenter
{
	/** @var UserModel @inject */
	public $userModel;

	/** @var DateTime */
	protected $lastAccess;

	/** @var UserTagModel @inject */
	public $userTagModel;

	/** @var TagModel @inject */
	public $tagModel;


	protected function startup()
	{
		parent::startup();

		$this->updateAccess();
	}


	private function updateAccess()
	{
		$access = $this->getSession('access');
		$now = new DateTime;

		// Get last access stored in DB
		if ($this->getUser()->isLoggedIn()) {
			$user = $this->userModel->getAll()
				->wherePrimary($this->getUser()->getId())
				->fetch();
			$lastInDb = $user->last_access;
		}

		// Store the newest access
		$this->lastAccess = \App\Modules\Core\Utils\DateTime::max($access->last ?? null, $lastInDb ?? null);

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


	private function shouldSyncToDb(DateTime $sessionLastInDb = null, DateTime $lastInDb = null, string $syncToDb) : bool
	{
		return !$sessionLastInDb
		|| ($lastInDb && $lastInDb > $sessionLastInDb)
		|| $sessionLastInDb < new DateTime('-' . $syncToDb);
	}
}
