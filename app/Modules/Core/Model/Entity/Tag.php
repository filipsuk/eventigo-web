<?php declare(strict_types=1);

namespace App\Modules\Core\Model\Entity;

use Nette\Database\IRow;

class Tag
{
	/** @var int */
	private $id;

	/** @var string */
	private $name;

	/** @var string */
	private $code;

	public static function createFromRow(IRow $tagRow)
	{
		$tag = new Tag();
		$tag->setId($tagRow['id'])
			->setName($tagRow['name'])
			->setCode($tagRow['code']);
		return $tag;
	}

	/**
	 * @return int
	 */
	public function getId(): int
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 * @return Tag
	 */
	public function setId(int $id): Tag
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 * @return Tag
	 */
	public function setName(string $name): Tag
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getCode(): string
	{
		return $this->code;
	}

	/**
	 * @param string $code
	 * @return Tag
	 */
	public function setCode(string $code): Tag
	{
		$this->code = $code;
		return $this;
	}

}
