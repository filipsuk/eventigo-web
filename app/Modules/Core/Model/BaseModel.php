<?php declare(strict_types=1);

namespace App\Modules\Core\Model;

use Nette\Database\Context;
use Nette\Database\Table\IRow;
use Nette\Database\Table\Selection;


abstract class BaseModel
{
	/**
	 * @var Context
	 */
	protected $database;

	/**
	 * @var string
	 */
	const TABLE_NAME = '';


	public function __construct(Context $database)
	{
		$this->database = $database;
	}


	public function getAll(): Selection
	{
		return $this->database->table($this::TABLE_NAME);
	}


	/**
	 * Inserts row in a table.
	 * @param  array|\Traversable|Selection array($column => $value)|\Traversable|Selection for INSERT ... SELECT
	 * @return IRow|int|bool Returns IRow or number of affected rows for Selection or table without primary key
	 */
	public function insert($data)
	{
		return $this->getAll()->insert($data);
	}


	/**
	 * Updates all rows in result set.
	 * Joins in UPDATE are supported only in MySQL
	 * @param  array|\Traversable ($column => $value)
	 * @return int number of affected rows
	 */
	public function update($data)
	{
		return $this->getAll()->update($data);
	}


	public function delete(array $data)
	{
		$this->getAll()->where($data)->delete();
	}
}
