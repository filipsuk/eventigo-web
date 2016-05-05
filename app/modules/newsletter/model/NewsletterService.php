<?php

namespace App\Modules\Newsletter\Model;


use App\Modules\Core\Model\EventModel;
use App\Modules\Core\Model\EventTagModel;
use App\Modules\Core\Model\UserModel;
use App\Modules\Core\Model\UserTagModel;
use Kdyby\Translation\Translator;
use Nette\Application\IPresenterFactory;
use Nette\Application\UI\ITemplateFactory;
use Nette\Application\UI\Presenter;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\DI\Container;
use Nette\Utils\DateTime;
use SendGrid;
use SendGrid\Email;

class NewsletterService
{
	/** @var UserNewsletterModel @inject */
	public $userNewsletterModel;

	/** @var NewsletterModel @inject */
	public $newsletterModel;

	/** @var UserModel @inject */
	public $userModel;

	/** @var UserTagModel @inject */
	public $userTagModel;

	/** @var EventModel @inject */
	public $eventModel;

	/** @var EventTagModel @inject */
	public $eventTagModel;

	/** @var string */
	private $apiKey;

	/** @var IPresenterFactory @inject */
	public $presenterFactory;

	/** @var ITemplateFactory @inject */
	public $templateFactory;

	/** @var  Template */
	protected $template;

	/** @var  Presenter */
	protected $presenter;

	/** @var  Container @inject */
	public $context;

	/** @var  Translator @inject */
	public $translator;


	public function setApiKey(string $apiKey) : self
	{
		$this->apiKey = $apiKey;
		return $this;
	}


	/**
	 * Creates new newsletter with content for giver user
	 *
	 * @param int $userId
	 * @return bool|int|\Nette\Database\Table\IRow
	 * @throws \App\Modules\Newsletter\Model\NoEventsFoundException
	 */
	public function createUserNewsletter($userId)
	{
		/**
		 * 1 Build array for template
		 *  1.1 Generate UserNewsletter hash
		 *  1.2 Get texts from NewsletterModel
		 *  1.3 Get events (grouped)
		 * 2. Render user's newsletter
		 * 3. Save user's newsletter with UserNewsletterModel
		 */
		
		$newsletter = $this->buildArrayForTemplate($userId); // TODO handle exception
		$content = $this->renderNewsletterContent($newsletter);
		$subject = $newsletter['subject'];
		$from = $newsletter['from'];

		return $this->userNewsletterModel->createNewsletter($userId, $from, $subject, $content, $newsletter['hash']);
	}

	/**
	 * Build array with newsletter text and events for render in template
	 *
	 * @param int $userId
	 * @return array
	 * @throws \App\Modules\Newsletter\Model\NoEventsFoundException
	 */
	public function buildArrayForTemplate(int $userId) : array
	{
		$baseUrl = $this->context->parameters['baseUrl'];
		$newsletterHash = $this->userNewsletterModel->generateUniqueHash();
		$userToken = $this->userModel->getUserToken($userId);
		
		$newsletter = $this->newsletterModel->getLatest();
		$newsletter['hash'] = $newsletterHash;
		$newsletter['eventGroups'] = $this->getGroupedEvents($userId);
		$newsletter['updatePreferencesUrl'] = $baseUrl . '/profile/settings/' . $userToken;
		$newsletter['unsubscribeUrl'] = $baseUrl . '/newsletter/unsubscribe/' . $newsletterHash;

		return $newsletter;
	}

	/**
	 * @param int[] $usersNewslettersIds
	 */
	public function sendNewsletters(array $usersNewslettersIds)
	{
		$usersNewsletters = $this->userNewsletterModel->getAll()->wherePrimary($usersNewslettersIds)->fetchAll();
		foreach ($usersNewsletters as $userNewsletter) {
			$sendGrid = new SendGrid($this->apiKey);
			$email = new Email;

			$email->addTo($userNewsletter->user->email)
				->setFrom($userNewsletter->from)
				->setFromName('Eventigo.cz')
				->setSubject($userNewsletter->subject)
				->setHtml($userNewsletter->content);
				//TODO: setText() - we should also send text format

			try {
				$sendGrid->send($email);
				$this->userNewsletterModel->getAll()->wherePrimary($userNewsletter->id)->update([
					'sent' => new DateTime,
				]);

			} catch (SendGrid\Exception $e) {
				// TODO log unsuccessful email send
			}
		}
	}

	/**
	 * Get events for user newsletter.
	 * Events are in groups like 'Next week', 'You may like' etc.
	 *
	 * @param int $userId
	 * @return array
	 * @throws \App\Modules\Newsletter\Model\NoEventsFoundException
	 */
	private function getGroupedEvents(int $userId) : array
	{
		$from = new DateTime('next monday');
		$to = $from->modifyClone('+ 1 week');
		$userTags = $this->userTagModel->getUserTagIds($userId);

		$returnArray = [
			[
				'title' => $this->translator->trans('newsletter.email.events.nextWeek'),
				'events' => []
			],
//			[
//				'title' => 'Další nově přidané akce', //TODO posilat dalsi akce
//				'events' => [
//					[
//						'name' => 'Skrz DEV cirkus',
//						'date' => 'Pondělí 25. 4. 2016, 18:00',
//						'hashtags' => '#programovani #php #nette',
//						'url' => '#'
//					],
//				]
//			]
		];

		$nextWeekEvents = $this->eventModel->getAllWithDates($userTags, $from, $to);
		if (count($nextWeekEvents) === 0) {
			throw new NoEventsFoundException("No events found for user $userId!");
		}

		foreach ($nextWeekEvents as $event) {
			$hashtags = $this->eventTagModel->getEventTagsString($event->id);

			$returnArray[0]['events'][] = [
				'name' => $event->name,
				'date' => $event->start,
				'hashtags' => $hashtags,
				'url' => $event->origin_url
			];
		}

		return $returnArray;
	}

	/**
	 * Render newsletter content from template (same thing as NewsletterPreseneter:dynamic)
	 *
	 * @param $newsletter
	 * @return string
	 */
	private function renderNewsletterContent($newsletter) : string
	{
		$this->presenter = $this->presenterFactory->createPresenter('Newsletter:Newsletter');
		$this->template = $this->templateFactory->createTemplate($this->presenter);
		$this->template->addFilter('datetime', function (DateTime $a, DateTime $b = null) {
			\App\Modules\Core\Utils\DateTime::setTranslator($this->translator);
			return \App\Modules\Core\Utils\DateTime::eventsDatetimeFilter($a, $b);
		});
		
		$this->template->newsletter = $newsletter;

		$templateFile = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'presenters' . DIRECTORY_SEPARATOR .
			'templates' . DIRECTORY_SEPARATOR . 'Newsletter' . DIRECTORY_SEPARATOR . 'dynamic.latte';
		$this->template->setFile($templateFile);

		return $this->template->getLatte()->renderToString($this->template->getFile(), $this->template->getParameters());
	}
}
