<?php declare(strict_types=1);

namespace App\Modules\Newsletter\Model;

use App\Modules\Core\Model\Entity\Event;
use App\Modules\Core\Model\EventModel;
use App\Modules\Core\Model\EventTagModel;
use App\Modules\Core\Model\UserModel;
use App\Modules\Core\Model\UserTagModel;
use App\Modules\Core\Utils\DateTime;
use App\Modules\Newsletter\Model\Entity\Newsletter;
use BadMethodCallException;
use DateInterval;
use Kdyby\Translation\Translator;
use Nette\Application\LinkGenerator;
use Nette\Application\UI\ITemplateFactory;
use Nette\Bridges\ApplicationLatte\ILatteFactory;
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
    private const CSS_FILE_PATH = __DIR__ . DIRECTORY_SEPARATOR . '..'
        . DIRECTORY_SEPARATOR . 'Presenters' . DIRECTORY_SEPARATOR . 'templates'
        . DIRECTORY_SEPARATOR . 'Newsletter' . DIRECTORY_SEPARATOR . 'build.css';

    /**
     * @var string[]
     */
    private const NEWSLETTER_UTM_PARAMETERS = [
        'utm_source' => 'newsletter',
        'utm_medium' => 'email'
    ];

    /**
     * @var UserNewsletterModel
     */
    private $userNewsletterModel;

    /**
     * @var NewsletterModel
     */
    private $newsletterModel;

    /**
     * @var UserModel
     */
    private $userModel;

    /**
     * @var UserTagModel
     */
    private $userTagModel;

    /**
     * @var EventModel
     */
    private $eventModel;

    /**
     * @var EventTagModel
     */
    private $eventTagModel;

    /**
     * @var ITemplateFactory
     */
    private $templateFactory;

    /**
     * @var  Container
     */
    private $context;

    /**
     * @var  Translator
     */
    private $translator;

    /**
     * @var  LinkGenerator
     */
    private $linkGenerator;

    /**
     * @var SendGrid
     */
    private $sendGrid;

    /**
     * @var  ILatteFactory
     */
    private $latteFactory;

    public function __construct(
        SendGrid $sendGrid,
        LinkGenerator $linkGenerator,
        Translator $translator,
        Container $context,
        ITemplateFactory $templateFactory,
        EventTagModel $eventTagModel,
        EventModel $eventModel,
        UserTagModel $userTagModel,
        UserModel $userModel,
        NewsletterModel $newsletterModel,
        UserNewsletterModel $userNewsletterModel,
        ILatteFactory $latteFactory
    ) {
        $this->sendGrid = $sendGrid;
        $this->linkGenerator = $linkGenerator;
        $this->translator = $translator;
        $this->context = $context;
        $this->templateFactory = $templateFactory;
        $this->eventTagModel = $eventTagModel;
        $this->eventModel = $eventModel;
        $this->userTagModel = $userTagModel;
        $this->userModel = $userModel;
        $this->newsletterModel = $newsletterModel;
        $this->userNewsletterModel = $userNewsletterModel;
        $this->latteFactory = $latteFactory;
    }

    public function createDefaultNewsletter(): IRow
    {
        $parameters = $this->context->getParameters()['newsletter'];
        // @todo: refactor to use immutability with static ctor or factory
        $newsletter = new Newsletter;
        $newsletter->setSubject($parameters['defaultSubject'] ?? '');
        $newsletter->setFrom($parameters['defaultAuthor']['email'] ?? '');
        $newsletter->setAuthor($parameters['defaultAuthor']['name'] ?? '');
        $newsletter->setIntroText($this->renderRecentlyApprovedEvents($this->getApprovedEventsSinceLastNewsletter()));
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
        $newsletter = $this->prepareLinks($newsletter, $userToken, $baseUrl, $newsletterHash);

        return $newsletter;
    }

    /**
     * @param mixed[] $templateData
     * @param mixed $userToken
     * @return mixed[]
     */
    public function prepareLinks(array $templateData, $userToken, string $baseUrl, string $newsletterHash): array
    {
        $templateData['updatePreferencesUrl'] = $this->linkGenerator->link(
            'Front:Profile:settings',
            array_merge([
                'token' => $userToken, 'utm_campaign' => 'newsletterButton'], self::NEWSLETTER_UTM_PARAMETERS
            )
        );
        $templateData['feedUrl'] = $this->linkGenerator->link(
            'Front:Homepage:default',
            array_merge([
                'token' => $userToken, 'utm_campaign' => 'newsletterButton'], self::NEWSLETTER_UTM_PARAMETERS
            )
        );
        $templateData['unsubscribeUrl'] = $baseUrl . '/newsletter/unsubscribe/' . $newsletterHash;

        return $templateData;
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
            $content = new SendGrid\Content('text/html', $userNewsletter->content);
            $mail = new SendGrid\Mail($from, $subject, $to, $content);
            $mail->addCategory('newsletter');

            try {
                $this->sendGrid->client->mail()->send()->post($mail);
                $this->userNewsletterModel->getAll()->wherePrimary($userNewsletter->id)->update([
                    'sent' => new NetteDateTime,
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

        $showAbroad = $this->userModel->showAbroadEvents($userId);
        $nextWeekEvents = $this->eventModel->getAllWithDates($userTags, $from, $to, null, $showAbroad);

        if (! $this->context->parameters['newsletter']['sendNewsletterWithNoEvents'] && count($nextWeekEvents) === 0) {
            throw new NoEventsFoundException("No events found for user $userId!");
        }

        foreach ($nextWeekEvents as $event) {
            $hashtags = $this->eventTagModel->getEventTagsString($event->id);

            $returnArray[0]['events'][] = [
                'name' => $event->name,
                'date' => $event->start,
                'hashtags' => $hashtags,
                'url' => $this->linkGenerator->link('Front:Redirect:', [$event->origin_url]),
                'venue' => $event->venue,
                'countryCode' => $event->country_id,
            ];
        }

        return $returnArray;
    }

    /**
     * Get events approved since 12 hours after last newsletter was created.
     * @return Event[]
     */
    private function getApprovedEventsSinceLastNewsletter(): array
    {
        /** @var NetteDateTime $lastNewsletterCreated */
        $lastNewsletterCreated = $this->newsletterModel->getLatest()['created'];

        return $this->eventModel->getApprovedEventsByDate(
            $lastNewsletterCreated->add(new DateInterval('PT12H')),
            null,
            true
        );
    }

    /**
     * Render newsletter content from template (same thing as NewsletterPreseneter:dynamic).
     *
     * @param mixed[] $newsletter
     */
    private function renderNewsletterContent(array $newsletter): string
    {
        /** @var Template $template */
        $template = $this->templateFactory->createTemplate();
        $template->addFilter('datetime', function (NetteDateTime $a, ?NetteDateTime $b = null) {
            DateTime::setTranslator($this->translator);

            return DateTime::eventsDatetimeFilter($a, $b);
        });

        $template->add('newsletter', self::inlineCss($newsletter));

        $templateFile = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Presenters'
            . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'Newsletter'
            . DIRECTORY_SEPARATOR . 'dynamic.latte';
        $template->setFile($templateFile);

        $template->getLatte()->addProvider('uiControl', $this->linkGenerator); // pro Latte 2.4

        // @todo: use latte directly
        return $template->getLatte()->renderToString(
            $template->getFile(), $template->getParameters()
        );
    }

    /**
     * @param Event[] $events
     */
    private function renderRecentlyApprovedEvents(array $events): string
    {
        $templateFile = __DIR__ . '/../Presenters/templates/Newsletter/newEvents.latte';
        $latte = $this->latteFactory->create();

        return $latte->renderToString($templateFile, ['events' => $this->mapEventLinksToRedirect($events)]);
    }

    /**
     * Map event urls to use Redirect presenter.
     * @param Event[] $events
     * @return Event[]
     */
    private function mapEventLinksToRedirect(array $events): array
    {
        return array_map(
            function ($event) {
                /** @var Event $event */
                $event->setOriginUrl($this->linkGenerator->link('Front:Redirect:', [$event->getOriginUrl()]));

                return $event;
            },
            $events
        );
    }
}
