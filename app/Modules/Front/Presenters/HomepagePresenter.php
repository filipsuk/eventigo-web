<?php declare(strict_types=1);

namespace App\Modules\Front\Presenters;

use App\Modules\Core\Model\EventModel;
use App\Modules\Core\Model\UserModel;
use App\Modules\Core\Utils\Collection;
use App\Modules\Core\Utils\Helper;
use App\Modules\Email\Model\EmailService;
use App\Modules\Front\Components\EventsList\EventsList;
use App\Modules\Front\Components\EventsList\EventsListFactoryInterface;
use App\Modules\Front\Components\Sign\SignIn;
use App\Modules\Front\Components\Sign\SignInFactoryInterface;
use App\Modules\Front\Components\SubscriptionTags\SubscriptionTags;
use App\Modules\Front\Components\SubscriptionTags\SubscriptionTagsFactoryInterface;
use Kdyby\Facebook\Dialog\LoginDialog;
use Kdyby\Facebook\FacebookApiException;
use Nette\Security\Identity;
use Nette\Utils\DateTime;
use Nette\Utils\Html;
use Tracy\Debugger;


final class HomepagePresenter extends AbstractBasePresenter
{
	/**
	 * @var EventModel @inject
	 */
	public $eventModel;

	/**
	 * @var SubscriptionTagsFactoryInterface @inject
	 */
	public $subscriptionTags;

	/**
	 * @var EventsListFactoryInterface @inject
	 */
	public $eventsListFactory;

	/**
	 * @var \Kdyby\Facebook\Facebook @inject
	 */
	public $facebook;

	/**
	 * @var SignInFactoryInterface @inject
	 */
	public $signInFactory;

	/**
	 * @var EmailService @inject
	 */
	public $emailService;


	/**
	 * @param string[] $tags
	 * @param null $token User token for login
	 * @throws \Nette\Application\BadRequestException
	 * @throws \Nette\Application\AbortException
	 */
	public function renderDefault(array $tags, $token = null): void
	{
		// Try to log in the user with provided token
		if ($token) {
			$this->loginWithToken($token);
			$this->redirect('Homepage:', Helper::extractUtmParameters($this->getParameters()));
		}

		if (! $tags) {
			$section = $this->getSession('subscriptionTags');
			$tags = $section->tags;
		}

		$this->template->eventModel = $this->eventModel;
		$this->template->tags = $this->tagModel->getAll();

		$tags = (array) $tags;
		// TODO do this more general
		// Remove tags with no events
		$activeTags = $this->tagModel->getByMostEvents()->fetchPairs(null, 'code');
		foreach ($tags as $tagsGroupName => &$tagsGroup) {
			foreach ($tagsGroup as $i => &$tag) {
				if (! in_array($tag, $activeTags)) {
					unset($tags[$tagsGroupName][$i]);
				}
			}
		}

		$this['subscriptionTags']['form']->setDefaults(['tags' => $tags ?? []]);
	}


	public function renderDiscover(): void
	{
		$section = $this->getSession('discover');
		$tags = $section->tags ?: $section->tags = [];
		$tags = (array) $tags;

		// TODO do this more general
		// Remove tags with no events
		$activeTags = $this->tagModel->getByMostEvents()->fetchPairs(null, 'code');
		foreach ($tags as $tagsGroupName => &$tagsGroup) {
			foreach ($tagsGroup as $i => &$tag) {
				if (! in_array($tag, $activeTags)) {
					unset($tags[$tagsGroupName][$i]);
				}
			}
		}

		$this['subscriptionTags']['form']->setDefaults(['tags' => $tags]);
	}


	public function handleFollowTag(string $tagCode): void
	{
		$tag = $this->tagModel->getAll()->where(['code' => $tagCode]);

		$section = $this->session->getSection('subscriptionTags');

		// Follow tag
		if ($this->user->getId()) {
			$this->userTagModel->insert([
				'user_id' => $this->user->id,
				'tag_id' => $tag->id,
			]);
		} else {
			$section->tags[] = $tagCode;
		}

		// Refresh data
		$tagsIds = $this->tagModel->getAll()->where('code', $section->tags)->fetchPairs(null, 'id');
		$this->events = $this->eventModel->getAllWithDates($tagsIds, new DateTime);
		$this->followedTags = $section->tags;

		$this['eventsList']->redrawControl();
	}


	public function handleUnfollowTag(string $tagCode): void
	{
		$tag = $this->tagModel->getAll()->where(['code' => $tagCode]);
		$section = $this->session->getSection('subscriptionTags');

		// Unfollow tag
		if ($this->user->id) {
			$this->userTagModel->delete([
				'user_id' => $this->user->id,
				'tag_id' => $tag->id,
			]);
		} else {
			if (($index = array_search($tagCode, $section->tags)) !== FALSE) {
				unset($section->tags[$index]);
			}
		}

		// Refresh data
		$tagsIds = $this->tagModel->getAll()->where('code', $section->tags)->fetchPairs(null, 'id');
		$this->events = $this->eventModel->getAllWithDates($tagsIds, new DateTime);
		$this->followedTags = $section->tags;

		$this['eventsList']->redrawControl();
	}


	protected function createComponentSubscriptionTags(): SubscriptionTags
	{
		$control = $this->subscriptionTags->create();

		$control->onEmailExists[] = function ($email) {
			// Send email with login
			$user = $this->userModel->getUserByEmail($email);
			$this->emailService->sendLogin($email, $user->token);

			$this->flashMessage(
				'<i class="fa fa-envelope"></i> ' .
				$this->translator->translate('front.subscription.message.emailExists',
					['email' => Html::el('strong')->setText($email)]),
				'success');

			$this->redrawControl('flash-messages');
		};

		$control->onSuccess[] = function ($email) {
			$this->getUser()->login(UserModel::SUBSCRIPTION_LOGIN, $email);

			$this->flashMessage(
				'<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>' .
				$this->translator->translate('front.subscription.message.success',
					['email' => Html::el('strong')->setText($email)]),
				'success');

			// TODO refactor duplicate code
			// Redirect to settings if no tags
			$section = $this->getSession('subscriptionTags');
			$chosenTags = Collection::getNestedValues($section->tags ?? []);
			if (isset($chosenTags) && count($chosenTags) === 0) {
				$this->flashMessage($this->translator->translate('front.profile.settings.afterLogin', 'info'));
				$this->redirect('Profile:settings');
			}

			$this->redirect('Homepage:');
		};

		$control->onChange[] = function () {
			$this['eventsList']->redrawControl();
			$this->redrawControl('flash-messages');
		};

		return $control;
	}


	protected function createComponentEventsList(): EventsList
	{
		if (! $this->user->getId() || $this->getAction() !== 'default') {
			$section = $this->getSession($this->getAction() === 'discover' ? 'discover' : 'subscriptionTags');
			$tags = Collection::getNestedValues($section->tags ?? []);

			// TODO do this more general
			// Remove tags with no events
			$activeTags = $this->tagModel->getByMostEvents()->fetchPairs(null, 'code');
			foreach ($tags as $i => $tag) {
				if (! in_array($tag, $activeTags)) {
					unset($tags[$i]);
				}
			}

			$tagsIds = $this->tagModel->getAll()->where('code', $tags)->fetchPairs(null, 'id');
		} else {
			$tagsIds = $this->userTagModel->getAll()
				->where('user_id', $this->getUser()->getId())
				->fetchPairs(null, 'id');
		}

		$events = $this->eventModel->getAllWithDates($tagsIds, new DateTime, null, $this->lastAccess);

		return $this->eventsListFactory->create($events);
	}


	protected function createComponentFbLogin(): LoginDialog
	{
		/** @var \Kdyby\Facebook\Dialog\LoginDialog $dialog */
		$dialog = $this->facebook->createDialog('login');

		$dialog->onResponse[] = function (LoginDialog $dialog) {
			$fb = $dialog->getFacebook();

			if (! $fb->getUser()) {
				$this->flashMessage($this->translator->translate('front.homepage.fbLogin.failed'), 'danger');
				return;
			}

			try {
				$me = $fb->api('/me?fields=email,first_name,name');

				if (! $existing = $this->userModel->findByFacebookId($fb->getUser())) {
					$user = $this->userModel->signInViaFacebook($me);

					// TODO move to user tag service
					$section = $this->getSession('subscriptionTags');
					$chosenTags = Collection::getNestedValues($section->tags ?? []);
					$tags = $this->tagModel->getAll()->where('code IN (?)', $chosenTags)->fetchAll();
					foreach ($tags as $tag) {
						$this->userTagModel->insert([
							'tag_id' => $tag->id,
							'user_id' => $user->id,
						]);
					}
				}

				$existing = $this->userModel->updateFacebook($me, $fb->getAccessToken());

				$this->getUser()->login(new Identity($existing->id, null, $existing->toArray()));

			} catch (FacebookApiException $exception) {
				Debugger::log($exception, 'facebook');
				$this->flashMessage($this->translator->translate('front.homepage.fbLogin.failed'), 'danger');
				$this->redirect('this');
			}

			// TODO refactor duplicate code
			// Redirect to settings if no tags
			if (isset($chosenTags) && count($chosenTags) === 0) {
				$this->flashMessage($this->translator->translate('front.profile.settings.afterLogin', 'info'));
				$this->redirect('Profile:settings');
			}

			$this->redirect('this');
		};

		return $dialog;
	}


	protected function createComponentSignIn(): SignIn
	{
		$control = $this->signInFactory->create();

		$control->onSuccess[] = function (string $email) {
			$this->flashMessage(
			 	'<i class="fa fa-envelope"></i> ' .
			 	$this->translator->translate('front.signIn.form.success', ['email' => $email])
			);
			$this->redrawControl('flash-messages');
		};

		return $control;
	}
}
