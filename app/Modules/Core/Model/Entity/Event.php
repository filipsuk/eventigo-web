<?php declare(strict_types=1);

namespace App\Modules\Core\Model\Entity;

use Nette\Database\IRow;
use Nette\Utils\DateTime;

class Event
{
	/** @var int */
	private $id;
	
	/** @var string */
	private $name;

	/** @var string */
	private $description;

	/** @var string */
	private $originUrl;
	
	/** @var DateTime */
	private $start;

	/** @var DateTime */
	private $end;

	/** @var string */
	private $image;
	
	/** @var int Size of event */
	private $rate;

	public function __construct(
		int $id = null,
		string $name,
		string $description = null,
		string $originUrl = null,
		DateTime $start,
		DateTime $end = null,
		string $image = null,
		int $rate = null
	)
	{
		$this->id = $id;
		$this->name = $name;
		$this->description = $description;
		$this->originUrl = $originUrl;
		$this->start = $start;
		$this->end = $end;
		$this->image = $image;
		$this->rate = $rate;
	}


	public static function createFromRow(IRow $eventRow): Event
	{
		return new Event(
			$eventRow['id'],
			$eventRow['name'],
			$eventRow['description'],
			$eventRow['origin_url'],
			$eventRow['start'],
			$eventRow['end'],
			$eventRow['image'],
			$eventRow['rate']
		);
	}

	public function getId(): int
	{
		return $this->id;
	}
	
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * TODO set return type after upgrade to PHP 7.1
	 * @return string|null
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * TODO set return type after upgrade to PHP 7.1
	 * @return string|null
	 */
	public function getOriginUrl()
	{
		return $this->originUrl;
	}

	public function getStart(): DateTime
	{
		return $this->start;
	}

	/**
	 * TODO set return type after upgrade to PHP 7.1
	 * @return DateTime|null
	 */
	public function getEnd()
	{
		return $this->end;
	}

	/**
	 * TODO set return type after upgrade to PHP 7.1
	 * @return string|null
	 */
	public function getImage()
	{
		return $this->image;
	}

	/**
	 * TODO set return type after upgrade to PHP 7.1
	 * @return int|null
	 */
	public function getRate()
	{
		return $this->rate;
	}

	public static function calculateRateByAttendeesCount(int $count)
	{
		if ($count <= 50) { return 1; }
		elseif ($count <= 200) { return 2; }
		elseif ($count <= 500) { return 3; }
		elseif ($count <= 1000) { return 4; }
		else {return 5;}
	}

}
