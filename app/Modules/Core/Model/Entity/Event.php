<?php declare(strict_types=1);

namespace App\Modules\Core\Model\Entity;

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

	public function getId(): int
	{
		return $this->id;
	}
	
	public function getName(): string
	{
		return $this->name;
	}

	public function setName(string $name): self
	{
		$this->name = $name;
		return $this;
	}

	public function getDescription(): string
	{
		return $this->description;
	}

	public function setDescription(string $description): self
	{
		$this->description = $description;
		return $this;
	}

	public function getOriginUrl(): string
	{
		return $this->originUrl;
	}

	public function setOriginUrl(string $originUrl): self
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

	public function getEnd(): DateTime
	{
		return $this->end;
	}

	public function setEnd(DateTime $end): self
	{
		$this->end = $end;
		return $this;
	}

	public function getImage(): string
	{
		return $this->image;
	}

	public function setImage(string $image): self
	{
		$this->image = $image;
		return $this;
	}

	public function getRate(): int
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
