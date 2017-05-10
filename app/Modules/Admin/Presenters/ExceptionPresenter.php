<?php declare(strict_types=1);

namespace App\Modules\Admin\Presenters;

use App\Modules\Core\Model\EventModel;
use Nette\Application\Responses\FileResponse;
use Tracy\Debugger;

final class ExceptionPresenter extends AbstractBasePresenter
{
    /**
     * @var EventModel @inject
     */
    public $eventModel;

    /**
     * Renders html exception from log directory with provided filename.
     *
     * @throws \Nette\Application\AbortException
     */
    public function renderDefault(string $filename): void
    {
        $file = Debugger::$logDirectory . DIRECTORY_SEPARATOR . $filename;
        $this->sendResponse(new FileResponse($file, $filename, 'text/html', false));
    }
}
