<?php declare(strict_types=1);

namespace App\Modules\Admin\Components\DataTable;

use App\Modules\Core\Components\AbstractBaseControl;
use Kdyby\Translation\Translator;
use Nette\Application\Responses\JsonResponse;
use Nette\Database\Table\Selection;

abstract class AbstractDataTable extends AbstractBaseControl
{
	/**
	 * @var mixed[]
	 */
	protected $data = [];

	/**
	 * @var Selection
	 */
	protected $dataSource;

	public function __construct(Translator $translator, Selection $dataSource)
	{
		parent::__construct($translator);
		$this->dataSource = $dataSource;
	}

	/**
	 * @return mixed[]
	 */
	abstract public function generateJson(): array;

	public function handleJson(): void
	{
		$json = $this->generateJson();
		$this->presenter->sendResponse(new JsonResponse($json));
	}

	public function getLang(): string
	{
		return '//cdn.datatables.net/plug-ins/1.10.11/i18n/Czech.json';
	}
}
