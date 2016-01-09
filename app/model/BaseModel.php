<?php

namespace App\Model;

use Nette\Database\Context;
use Nette\Database\Table\Selection;


abstract class BaseModel
{
	/** @var Context */
	private $database;

	const TABLE_NAME = '';


	public function __construct(Context $database)
	{
		$this->database = $database;
	}


	public function getAll() : Selection
	{
		return $this->database->table($this::TABLE_NAME);
	}
}