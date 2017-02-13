<?php declare(strict_types=1);

namespace App\Modules\Admin\Components\DataTable;

use App\Modules\Core\Components\BaseControl;
use Kdyby\Translation\Translator;
use Nette\Database\Table\Selection;


abstract class DataTable extends BaseControl
{
	/** @var array */
	protected $data = [];

	/** @var Selection */
	protected $dataSource;


	public function __construct(Translator $translator, Selection $dataSource)
	{
		parent::__construct($translator);
		$this->dataSource = $dataSource;
	}


	/**
	 * @return array
	 */
	abstract public function generateJson();


	public function handleJson()
	{
		$json = $this->generateJson();
		$this->presenter->sendResponse(new \Nette\Application\Responses\JsonResponse($json));
	}


	public function getLang() : string
	{
		return '//cdn.datatables.net/plug-ins/1.10.11/i18n/Czech.json';
	}
}