<?php declare(strict_types=1);

namespace App\Modules\Front\Presenters;

use App\Modules\Core\Model\EventModel;
use App\Modules\Core\Model\TagModel;
use App\Modules\Core\Model\UserModel;
use App\Modules\Core\Model\UserTagModel;
use App\Modules\Core\Presenters\AbstractBasePresenter;
use App\Modules\Front\Components\EventsList\EventsListFactoryInterface;
use App\Modules\Front\Components\Settings\Settings;
use App\Modules\Front\Components\Settings\SettingsFactoryInterface;
use App\Modules\Front\Components\Tags\Tags;
use App\Modules\Front\Components\Tags\TagsFactoryInterface;


final class ProfilePresenter extends AbstractBasePresenter
{
	/**
	 * @var EventModel @inject
	 */
	public $eventModel;

	/**
	 * @var TagModel @inject
	 */
	public $tagModel;

	/**
	 * @var TagsFactoryInterface @inject
	 */
	public $tagsFactory;

	/**
	 * @var EventsListFactoryInterface @inject
	 */
	public $eventsListFactory;

	/**
	 * @var SettingsFactoryInterface @inject
	 */
	public $settingsFactory;

	/**
	 * @var UserTagModel @inject
	 */
	public $userTagModel;

	/**
	 * @var UserModel @inject
	 */
	public $userModel;

	protected function startup(): void
	{
		parent::startup();

		if (!$this->getUser()->isLoggedIn()) {
			$this->flashMessage($this->translator->translate('front.profile.settings.notLoggedIn'));
			$this->redirect('Homepage:default');
		}
	}

	public function actionSettings(?string $token = null): void
	{
		// Try to log in the user with provided token
		if ($token) {
			$this->loginWithToken($token);
		}
	}


	public function renderSettings(): void
	{
		$this->template->userData = $this->userModel->getAll()
			->wherePrimary($this->getUser()->getId())->fetch();

		$userTags = $this->userTagModel->getUsersTags($this->user->getId());
		$this['tags']['form']->setDefaults(['tags' => $userTags]);

		$user = $this->userModel->getAll()->wherePrimary($this->getUser()->getId())->fetch();
		$this['settings-form']->setDefaults(['newsletter' => $user->newsletter]);
	}


	protected function createComponentTags(): Tags
	{
		$control = $this->tagsFactory->create();

		$control->onChange[] = function () {
			$this['tags']->redrawControl();
			$this->redrawControl('flash-messages');
		};

		return $control;
	}


	protected function createComponentSettings(): Settings
	{
		return $this->settingsFactory->create();
	}
}
