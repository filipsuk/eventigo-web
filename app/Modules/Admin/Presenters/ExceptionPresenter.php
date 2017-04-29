<?php declare(strict_types=1);

namespace App\Modules\Admin\Presenters;

use App\Modules\Core\Model\EventModel;
use Nette\Application\Responses\FileResponse;
use Tracy\Debugger;


class ExceptionPresenter extends BasePresenter
{
	/**
	 * @var EventModel @inject
	 */
	public $eventModel;

	/**
	 * Renders html exception from log directory with provided filename
	 *
	 * @param $filename string Filename of exception html file in log directory
	 * @throws \Nette\Application\AbortException
	 */
	public function renderDefault($filename)
	{
		$file = Debugger::$logDirectory . DIRECTORY_SEPARATOR . $filename;
		$this->sendResponse(new FileResponse($file, $filename, 'text/html', false));
	}
}
