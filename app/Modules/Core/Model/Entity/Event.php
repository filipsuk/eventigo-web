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

	public static function createFromRow(IRow $eventRow): Event
	{
		$event = new Event();
		$event
			->setId($eventRow['id'])
			->setName($eventRow['name'])
			->setDescription($eventRow['description'])
			->setOriginUrl($eventRow['origin_url'])
			->setStart($eventRow['start'])
			->setEnd($eventRow['end'])
			->setImage($eventRow['image'])
			->setRate($eventRow['rate']);

		return $event;
	}

	public function getId(): int
	{
		return $this->id;
	}

	public function setId(int $id): Event
	{
		$this->id = $id;
		return $this;
	}
	
	public function getName(): string
	{
		return $this->name;
	}

	/* TODO After upgrade to 7.1: setName(?string $name): self */
	public function setName($name): self
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * TODO set return type after upgrade to PHP 7.1
	 * @return string|null
	 */
	public function getDescription()
	{
		return $this->description;
	}

	public function setDescription(string $description): self
	{
		$this->description = $description;
		return $this;
	}

	/**
	 * TODO set return type after upgrade to PHP 7.1
	 * @return string|null
	 */
	public function getOriginUrl()
	{
		return $this->originUrl;
	}

	/* TODO After upgrade to 7.1: setOriginUrl(?string $originUrl): self */
	public function setOriginUrl($originUrl): self
	{
		$this->originUrl = $originUrl;
		return $this;
	}

	public function getStart(): DateTime
	{
		return $this->start;
	}

	public function setStart(DateTime $start): self
	{
		$this->start = $start;
		return $this;
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
	 * TODO set parameter type after upgrade to PHP 7.1
	 */
	public function setEnd($end): self
	{
		$this->end = $end;
		return $this;
	}

	/**
	 * TODO set return type after upgrade to PHP 7.1
	 * @return string|null
	 */
	public function getImage()
	{
		return $this->image;
	}

	/* TODO After upgrade to 7.1: setImage(?string $image): self */
	public function setImage($image): self
	{
		$this->image = $image;
		return $this;
	}

	/**
	 * TODO set return type after upgrade to PHP 7.1
	 * @return int|null
	 */
	public function getRate()
	{
		return $this->rate;
	}

	public function setRate(int $rate): self
	{
		if ($rate < 1 || $rate > 5) {
			throw new \InvalidArgumentException('Rate value must be 1 to 5');
		}
		$this->rate = $rate;
		return $this;
	}

	/**
	 * Set event rate by number of attendees
	 */
	public function setRateByAttendeesCount(int $count)
	{
		if ($count <= 50) { $this->setRate(1); }
		elseif ($count <= 200) { $this->setRate(2); }
		elseif ($count <= 500) { $this->setRate(3); }
		elseif ($count <= 1000) { $this->setRate(4); }
		else {$this->setRate(5);}
	}

}
