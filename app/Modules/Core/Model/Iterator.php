<?php declare(strict_types=1);

namespace App\Modules\Core\Model;

use Iterator as PhpIterator;
use Nette\Database\Table\IRow;


class Iterator implements PhpIterator
{
	/**
	 * @var int
	 */
	protected $index;

	/**
	 * @var IRow[]
	 */
	protected $data;


	/**
	 * @param IRow[] $data
	 */
	public function __construct(array $data)
	{
		$this->data = array_values($data);
	}


	public function current(): ?IRow
	{
		return $this->valid() ? $this->data[$this->index] : NULL;
	}


	public function next(): ?IRow
	{
		++$this->index;
		return $this->current();
	}


	public function key(): int
	{
		return $this->index;
	}


	public function valid(): bool
	{
		return array_key_exists($this->index, $this->data);
	}


	public function rewind(): void
	{
		$this->index = 0;
	}
}