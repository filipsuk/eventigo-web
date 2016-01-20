<?php

namespace App\Model;

use Nette\Database\Table\IRow;


class Iterator implements \Iterator
{
	/** @var int */
	protected $index;

	/** @var IRow[] */
	protected $data;


	public function __construct(array $data)
	{
		$this->data = array_values($data);
	}


	public function current()
	{
		return $this->valid() ? $this->data[$this->index] : NULL;
	}


	public function next()
	{
		++$this->index;
		return $this->current();
	}


	public function key() : int
	{
		return $this->index;
	}


	public function valid() : bool
	{
		return array_key_exists($this->index, $this->data);
	}


	public function rewind()
	{
		$this->index = 0;
	}


	protected function last()
	{
		if ($this->index) {
			return $this->data[$this->index - 1];
		} else {
			return $this->current();
		}
	}
}