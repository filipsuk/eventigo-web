<?php

namespace App\Modules\Front\Presenters;

use App\Modules\Core\Model\EventRedirectModel;
use App\Modules\Core\Model\EventModel;
use App\Modules\Core\Presenters\BasePresenter;


class RedirectPresenter extends BasePresenter
{
	/** @var EventModel @inject */
	public $eventModel;

	/** @var EventRedirectModel @inject */
	public $eventRedirectModel;


	public function renderDefault(string $url)
	{
		if ($this->getUser()->isLoggedIn()) {
			// Find event with same url
			$events = $this->eventModel->getAll()->where('origin_url', $url)->fetchAll();

			foreach ($events as $event) {
				// Count the redirect
				$this->eventRedirectModel->insert([
					'event_id' => $event->id,
					'user_id' => $this->getUser()->getId(),
				]);
			}
		}

		$this->redirectUrl($url);
	}
}
