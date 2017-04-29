<?php declare(strict_types=1);

namespace App\Modules\Newsletter\Model;

use App\Modules\Core\Model\EventModel;
use App\Modules\Core\Model\EventTagModel;
use App\Modules\Core\Model\UserModel;
use App\Modules\Core\Model\UserTagModel;
use App\Modules\Core\Utils\DateTime;
use App\Modules\Newsletter\Model\Entity\Newsletter;
use BadMethodCallException;
use Kdyby\Translation\Translator;
use Nette\Application\IPresenterFactory;
use Nette\Application\LinkGenerator;
use Nette\Application\UI\ITemplateFactory;
use Nette\Application\UI\Presenter;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\Database\Table\IRow;
use Nette\DI\Container;
use Nette\Utils\DateTime as NetteDateTime;
use Pelago\Emogrifier;
use SendGrid;
use SendGrid\Email;
use Throwable;
use Tracy\Debugger;

final class NewsletterService
{
	/**
	 * Path to css file used for css inline of newsletter texts html.
	 *
	 * @var string
	 */
	const CSS_FILE_PATH = __DIR__ . DIRECTORY_SEPARATOR . '..'
		. DIRECTORY_SEPARATOR . 'Presenters' . DIRECTORY_SEPARATOR . 'templates'
		. DIRECTORY_SEPARATOR . 'Newsletter' . DIRECTORY_SEPARATOR . 'build.css';

	/**
	 * @var string[]
	 */
	const NEWSLETTER_UTM_PARAMETERS = [
		'utm_source' => 'newsletter',
		'utm_medium' => 'email'
	];
	/**
	 * @var UserNewsletterModel @inject
	 */
	public $userNewsletterModel;

	/**
	 * @var NewsletterModel @inject
	 */
	public $newsletterModel;

	/**
	 * @var UserModel @inject
	 */
	public $userModel;

	/**
	 * @var UserTagModel @inject
	 */
	public $userTagModel;

	/**
	 * @var EventModel @inject
	 */
	public $eventModel;

	/**
	 * @var EventTagModel @inject
	 */
	public $eventTagModel;

	/**
	 * @var IPresenterFactory @inject
	 */
	public $presenterFactory;

	/**
	 * @var ITemplateFactory @inject
	 */
	public $templateFactory;

	/**
	 * @var  Container @inject
	 */
	public $context;

	/**
	 * @var  Translator @inject
	 */
	public $translator;

	/**
	 * @var  LinkGenerator @inject
	 */
	public $linkGenerator;

	/**
	 * @var SendGrid @inject
	 */
	public $sendGrid;

	/**
	 * @var  Template
	 */
	protected $template;

	/**
	 * @var  Presenter
	 */
	protected $presenter;

	public function createDefaultNewsletter(): IRow
	{
		$parameters = $this->context->getParameters()['newsletter'];
		// @todo: refactor to use immutability with static ctor or factory
		$newsletter = new Newsletter;
		$newsletter->setSubject($parameters['defaultSubject'] ?? '');
		$newsletter->setFrom($parameters['defaultAuthor']['email'] ?? '');
		$newsletter->setAuthor($parameters['defaultAuthor']['name'] ?? '');
		$newsletter->setIntroText('');
		$newsletter->setOutroText('');

		return $this->newsletterModel->createNewsletter($newsletter);
	}

	/**
	 * Creates new newsletter with content for giver user.
	 *
	 * @return bool|int|\Nette\Database\Table\IRow
	 * @throws \App\Modules\Newsletter\Model\NoEventsFoundException
	 */
	public function createUserNewsletter(int $userId)
	{
		/**
		 * 1 Build array for template
		 * 1.1 Generate UserNewsletter hash
		 * 1.2 Get texts from NewsletterModel
		 * 1.3 Get events (grouped)
		 * 2. Render user's newsletter
		 * 3. Save user's newsletter with UserNewsletterModel.
		 */
		$newsletter = $this->buildArrayForTemplate($userId); // TODO handle exception
		$content = $this->renderNewsletterContent($newsletter);
		$subject = $newsletter['subject'];
		$from = $newsletter['from'];

		return $this->userNewsletterModel->createNewsletter($userId, $from, $subject, $content, $newsletter['hash']);
	}

	/**
	 * Build array with newsletter text and events for render in template.
	 *
	 * @throws \App\Modules\Newsletter\Model\NoEventsFoundException
	 * @return mixed[]
	 */
	public function buildArrayForTemplate(int $userId): array
	{
		$baseUrl = $this->context->parameters['baseUrl'];
		$newsletterHash = $this->userNewsletterModel->generateUniqueHash();
		$userToken = $this->userModel->getUserToken($userId);

		$newsletter = $this->newsletterModel->getLatest();
		$newsletter['hash'] = $newsletterHash;
		$newsletter['eventGroups'] = $this->getGroupedEvents($userId);
		$newsletter['updatePreferencesUrl'] = $this->linkGenerator->link(
			'Front:Profile:settings',
			array_merge([
				'token' => $userToken, 'utm_campaign' => 'newsletterButton'], self::NEWSLETTER_UTM_PARAMETERS
			)
		);
		$newsletter['feedUrl'] = $this->linkGenerator->link(
			'Front:Homepage:default',
			array_merge([
				'token' => $userToken, 'utm_campaign' => 'newsletterButton'], self::NEWSLETTER_UTM_PARAMETERS
			)
		);
		$newsletter['unsubscribeUrl'] = $baseUrl . '/newsletter/unsubscribe/' . $newsletterHash;

		return $newsletter;
	}

	/**
	 * @param int[] $usersNewslettersIds
	 */
	public function sendNewsletters(array $usersNewslettersIds): void
	{
		$usersNewsletters = $this->userNewsletterModel->getAll()->wherePrimary($usersNewslettersIds)->fetchAll();
		foreach ($usersNewsletters as $userNewsletter) {
			$to = new Email(null, $userNewsletter->user->email);
			$from = new Email('Eventigo.cz', $userNewsletter->from);
			$subject = $userNewsletter->subject;
			$content = new SendGrid\Content('text/plain', $userNewsletter->content); // @todo: or html?
			//	->setCategory('newsletter') // what is this for
			// TODO: setText() - we should also send text format

			$mail = new SendGrid\Mail($from, $subject, $to, $content);

			try {
				$this->sendGrid->client->mail()->send()->post($mail);
				$this->userNewsletterModel->getAll()->wherePrimary($userNewsletter->id)->update([
					'sent' => new DateTime,
				]);
			} catch (Throwable $throwable) {
				// TODO log unsuccessful email send
			}
		}
	}

	/**
	 * Inline CSS styles of intro and outro text in newsletter
	 * TODO: Move this to admin when saving new newsletter.
	 *
	 * @param mixed[] $newsletter
	 * @return mixed
	 */
	public static function inlineCss(array $newsletter)
	{
		$css = file_get_contents(self::CSS_FILE_PATH);
		$emogrifier = new Emogrifier;
		$emogrifier->setCss($css);

		try {
			// Inline CSS of intro text
			if (! empty($newsletter['intro_text'])) {
				$emogrifier->setHtml($newsletter['intro_text']);
				$newsletter['intro_text'] = $emogrifier->emogrifyBodyContent();
			}

			// Inline CSS of outro text
			if (! empty($newsletter['outro_text'])) {
				$emogrifier->setHtml($newsletter['outro_text']);
				$newsletter['outro_text'] = $emogrifier->emogrifyBodyContent();
			}
		} catch (BadMethodCallException $e) {
			Debugger::log($e->getMessage());
		}

		return $newsletter;
	}

	/**
	 * Get events for user newsletter.
	 * Events are in groups like 'Next week', 'You may like' etc.
	 *
	 * @throws \App\Modules\Newsletter\Model\NoEventsFoundException
	 * @return mixed[][]
	 */
	private function getGroupedEvents(int $userId): array
	{
		$from = new NetteDateTime('next monday');
		$to = $from->modifyClone('+ 1 week');
		$userTags = $this->userTagModel->getUserTagIds($userId);

		$returnArray = [
			[
				'title' => $this->translator->trans('newsletter.email.events.nextWeek'),
				'events' => []
			],
			//TODO posilat dalsi akce
		];

		$nextWeekEvents = $this->eventModel->getAllWithDates($userTags, $from, $to);

		if (! $this->context->parameters['newsletter']['sendNewsletterWithNoEvents'] && count($nextWeekEvents) === 0) {
			throw new NoEventsFoundException("No events found for user $userId!");
		}

		foreach ($nextWeekEvents as $event) {
			$hashtags = $this->eventTagModel->getEventTagsString($event->id);

			$returnArray[0]['events'][] = [
				'name' => $event->name,
				'date' => $event->start,
				'hashtags' => $hashtags,
				'url' => $this->linkGenerator->link('Front:Redirect:', [$event->origin_url])
			];
		}

		return $returnArray;
	}

	/**
	 * Render newsletter content from template (same thing as NewsletterPreseneter:dynamic).
	 *
	 * @param mixed[] $newsletter
	 */
	private function renderNewsletterContent(array $newsletter): string
	{
		$this->template = $this->templateFactory->createTemplate();
		$this->template->addFilter('datetime', function (NetteDateTime $a, ?NetteDateTime $b = null) {
			DateTime::setTranslator($this->translator);

			return DateTime::eventsDatetimeFilter($a, $b);
		});

		$this->template->newsletter = self::inlineCss($newsletter);

		$templateFile = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Presenters'
			. DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'Newsletter'
			. DIRECTORY_SEPARATOR . 'dynamic.latte';
		$this->template->setFile($templateFile);

		$this->template->getLatte()->addProvider('uiControl', $this->linkGenerator); // pro Latte 2.4

		// @todo: use latte directly
		return $this->template->getLatte()->renderToString(
			$this->template->getFile(), $this->template->getParameters()
		);
	}
}
