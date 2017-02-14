<?php declare(strict_types=1);

namespace App\Modules\Front\Presenters;

use App\Modules\Core\Model\EventModel;
use App\Modules\Core\Model\TagModel;
use App\Modules\Core\Model\UserModel;
use App\Modules\Core\Model\UserTagModel;
use App\Modules\Front\Components\EventsList\EventsListFactory;
use App\Modules\Front\Components\Settings\SettingsFactory;
use App\Modules\Front\Components\Tags\ITagsFactory;


class ProfilePresenter extends \App\Modules\Core\Presenters\BasePresenter
{
	/** @var EventModel @inject */
	public $eventModel;

	/** @var TagModel @inject */
	public $tagModel;

	/** @var ITagsFactory @inject */
	public $tags;

	/** @var EventsListFactory @inject */
	public $eventsListFactory;

	/** @var SettingsFactory @inject */
	public $settingsFactory;

	/** @var UserTagModel @inject */
	public $userTagModel;

	/** @var UserModel @inject */
	public $userModel;

	protected function startup()
	{
		parent::startup();

		if (!$this->getUser()->isLoggedIn()) {
			$this->flashMessage($this->translator->translate('front.profile.settings.notLoggedIn'));
			$this->redirect('Homepage:default');
		}
	}

	public function actionSettings($token = null)
	{
		// Try to log in the user with provided token
		if ($token) {
			$this->loginWithToken($token);
		}
	}


	public function renderSettings()
	{
		$this->template->userData = $this->userModel->getAll()
			->wherePrimary($this->getUser()->getId())->fetch();

		$userTags = $this->userTagModel->getUsersTags($this->user->getId());
		$this['tags']['form']->setDefaults(['tags' => $userTags]);

		$user = $this->userModel->getAll()->wherePrimary($this->getUser()->getId())->fetch();
		$this['settings-form']->setDefaults(['newsletter' => $user->newsletter]);
	}


	protected function createComponentTags()
	{
		$control = $this->tags->create();

		$control->onChange[] = function () {
			$this['tags']->redrawControl();
			$this->redrawControl('flash-messages');
		};

		return $control;
	}


	protected function createComponentSettings()
	{
		return $this->settingsFactory->create();
	}
}
