<?php

namespace App\Modules\Front\Presenters;

use App\Modules\Core\Model\EventModel;
use App\Modules\Core\Model\TagModel;
use App\Modules\Core\Model\UserModel;
use App\Modules\Core\Model\UserTagModel;
use App\Modules\Core\Presenters\BasePresenter;
use App\Modules\Front\Components\EventsList\EventsListFactory;
use App\Modules\Front\Components\Settings\SettingsFactory;
use App\Modules\Front\Components\Tags\ITagsFactory;
use Nette\Application\BadRequestException;
use Nette\Security\Identity;


class ProfilePresenter extends BasePresenter
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


	public function actionSettings($token = null)
	{
		if (!$this->getUser()->isLoggedIn()) {
			if ($token === null || ($user = $this->userModel->getAll()
					->where('token', $token)->fetch()) === false) {
				throw new BadRequestException();
			}

			$this->getUser()->login(new Identity($user->id, null, $user->toArray()));
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


	public function createComponentTags()
	{
		$control = $this->tags->create();

		$control->onChange[] = function () {
			$this['tags']->redrawControl();
			$this->redrawControl('flash-messages');
		};

		return $control;
	}


	public function createComponentSettings()
	{
		return $this->settingsFactory->create();
	}
}
