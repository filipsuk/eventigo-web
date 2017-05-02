<?php declare(strict_types=1);

namespace App\Modules\Api\V1\Presenters;

use App\Modules\Api\V1\Model\EventApiService;
use Drahak\Restful\Application\UI\ResourcePresenter;
use Drahak\Restful\IResource;

class EventsPresenter extends ResourcePresenter
{
	/** @var EventApiService */
	private $eventApiService;

	public function __construct(EventApiService $eventApiService)
	{
		parent::__construct();
		$this->eventApiService = $eventApiService;
	}

	public function actionRead()
	{
		$this->resource = $this->eventApiService->getEvents();
		$this->sendResource(IResource::JSON);
	}

}
