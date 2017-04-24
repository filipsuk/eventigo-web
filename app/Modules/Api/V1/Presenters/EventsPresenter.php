<?php declare(strict_types=1);

namespace App\Modules\Api\V1\Presenters;

use Drahak\Restful\Application\BadRequestException;
use Drahak\Restful\Application\UI\ResourcePresenter;

class EventsPresenter extends ResourcePresenter
{

	public function actionRead(string $id = null)
	{
		if ($id) {
			$this->sendErrorResource(BadRequestException::methodNotSupported('Detail not implemented, use without id!'));
		}

		$this->resource = [[
			'hello' => 'world'
		]];
	}

}
