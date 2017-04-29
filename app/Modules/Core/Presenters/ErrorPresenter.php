<?php declare(strict_types=1);

namespace App\Modules\Core\Presenters;

use Nette;
use Nette\Application\IPresenter;
use Nette\Application\IResponse;
use Nette\Application\Request;
use Tracy\ILogger;

class ErrorPresenter implements IPresenter
{
	/**
	 * @var ILogger
	 */
	private $logger;

	public function __construct(ILogger $logger)
	{
		$this->logger = $logger;
	}

	public function run(Request $request): IResponse
	{
		$e = $request->getParameter('exception');

		if ($e instanceof Nette\Application\BadRequestException) {
			// $this->logger->log("HTTP code {$e->getCode()}:
			// {$e->getMessage()} in {$e->getFile()}:{$e->getLine()}", 'access');
			return new Nette\Application\Responses\ForwardResponse($request->setPresenterName('Core:Error4xx'));
		}

		$this->logger->log($e, ILogger::EXCEPTION);

		return new Nette\Application\Responses\CallbackResponse(function () {
			require __DIR__ . '/templates/Error/500.phtml';
		});
	}
}
