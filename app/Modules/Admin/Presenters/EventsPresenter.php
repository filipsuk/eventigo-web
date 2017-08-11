<?php declare(strict_types=1);

namespace App\Modules\Admin\Presenters;

use App\Modules\Admin\Components\EventForm\EventForm;
use App\Modules\Admin\Components\EventForm\EventFormFactoryInterface;
use App\Modules\Admin\Components\EventsTable\NotApprovedEventsTable;
use App\Modules\Admin\Components\EventsTable\NotApprovedEventsTableFactoryInterface;
use App\Modules\Admin\Model\SourceService;
use App\Modules\Core\Model\EventModel;
use App\Modules\Core\Utils\DateTime as EventigoDateTime;
use Nette\Application\Request;
use Nette\Utils\DateTime;

final class EventsPresenter extends AbstractBasePresenter
{
    /**
     * @var EventFormFactoryInterface
     */
    private $eventFormFactory;

    /**
     * @var EventModel
     */
    private $eventModel;

    /**
     * @var SourceService
     */
    private $sourceService;

    /**
     * @var NotApprovedEventsTableFactoryInterface
     */
    private $notApprovedEventsTableFactory;

    public function __construct(
        NotApprovedEventsTableFactoryInterface $notApprovedEventsTableFactory,
        SourceService $sourceService,
        EventModel $eventModel,
        EventFormFactoryInterface $eventFormFactory
    ) {
        $this->notApprovedEventsTableFactory = $notApprovedEventsTableFactory;
        $this->sourceService = $sourceService;
        $this->eventModel = $eventModel;
        $this->eventFormFactory = $eventFormFactory;
    }

    public function actionCreate(): void
    {
        $this['eventForm-form']->setDefaults([
            'country_id' => 'CZ',
        ]);
    }

    public function actionUpdate(int $id): void
    {
        $event = $this->eventModel->getAll()
            ->wherePrimary($id)
            ->fetch();

        $defaults = $event->toArray();
        $defaults['start'] = DateTime::from($defaults['start'])->format(EventigoDateTime::DATETIME_FORMAT);
        $defaults['end'] = $defaults['end']
            ? DateTime::from($defaults['end'])->format(EventigoDateTime::DATETIME_FORMAT)
            : null;
        $defaults['tags'] = [];
        foreach ($event->related('events_tags') as $eventTag) {
            $defaults['tags'][] = [
                'code' => $eventTag->tag->code,
                'rate' => $eventTag->rate,
            ];
        }

        if ($this->getRequest()->getMethod() === Request::FORWARD) {
            $defaults['state'] = EventModel::STATE_APPROVED;
        }

        // Set image from previous event in series if none
        if (! $defaults['image'] && $event['event_series_id']) {
            $previousEvent = $this->eventModel->findPreviousEvent($event['event_series_id']);
            if ($previousEvent) {
                $defaults['image'] = $previousEvent->image;
            }
        }

        $defaults['country_id'] = $defaults['country_id'] ?: 'CZ';

        $this['eventForm-form']->setDefaults($defaults);
    }

    public function renderUpdate(): void
    {
        $this->template->setFile(__DIR__ . '/templates/Events/create.latte');
    }

    public function handleCrawlSources(): void
    {
        $addedEvents = $this->sourceService->crawlSources();

        if ($addedEvents > 0) {
            $this->flashMessage($this->translator->translate('admin.events.crawlSources.success',
                $addedEvents, ['events' => $addedEvents]), 'success');
        } else {
            $this->flashMessage($this->translator->translate('admin.events.crawlSources.noEvents'));
        }

        $this->redirect('this');
    }

    public function actionApprove(int $id): void
    {
        $this->forward('update', $id);
    }

    protected function createComponentEventForm(): EventForm
    {
        $control = $this->eventFormFactory->create();

        $control->onCreate[] = function (): void {
            $this->flashMessage($this->translator->translate('admin.eventForm.success'), 'success');
            $this->redirect('Events:default');
        };

        $control->onUpdate[] = function (): void {
            $this->flashMessage($this->translator->translate('admin.eventForm.success'), 'success');
            $this->redirect('Events:default');
        };

        return $control;
    }

    protected function createComponentNotApprovedEventsTable(): NotApprovedEventsTable
    {
        return $this->notApprovedEventsTableFactory->create(
            $this->eventModel->getAll()
                ->where('state', EventModel::STATE_NOT_APPROVED)
                ->where('start > ?', new DateTime)
        );
    }
}
