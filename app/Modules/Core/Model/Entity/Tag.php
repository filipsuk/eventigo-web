<?php declare(strict_types=1);

namespace App\Modules\Core\Model\Entity;

use Nette\Database\IRow;

final class Tag
{
	/**
	 * @var int
	 */
	private $id;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $code;

	public function __construct(int $id, string $name, string $code)
	{
		$this->id = $id;
		$this->name = $name;
		$this->code = $code;
	}

	public static function createFromRow(IRow $tagRow): Tag
	{
		return new Tag($tagRow['id'], $tagRow['name'], $tagRow['code']);
	}

	public function getId(): int
	{
		return $this->id;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getCode(): string
	{
		return $this->code;
	}
}
