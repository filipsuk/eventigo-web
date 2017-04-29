<?php declare(strict_types=1);

namespace App\Modules\Core\Model\Entity;

use InvalidArgumentException;
use Nette\Database\IRow;
use Nette\Utils\DateTime;

final class Event
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
	private $description;

	/**
	 * @var string
	 */
	private $originUrl;

	/**
	 * @var DateTime
	 */
	private $start;

	/**
	 * @var DateTime
	 */
	private $end;

	/**
	 * @var string
	 */
	private $image;

	/**
	 * @var int Size of event
	 */
	private $rate;

	/** @var DateTime */
	private $created;

	public function __construct(
		int $id = null,
		string $name,
		string $description = null,
		string $originUrl = null,
		DateTime $start,
		DateTime $end = null,
		string $image = null,
		int $rate = null,
		DateTime $created = null
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
		$this->created = $created;
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
			$eventRow['rate'],
			$eventRow['created']
		);
	}

	public function getId(): int
	{
		return $this->id;
	}

	public function getHash(): string
	{
		if ($this->getId() && $this->getCreated()) {
			return md5($this->getId() . $this->getCreated()->getTimestamp());
		}

        throw new \RuntimeException('Could not calculate hash, "id" or "created" field not set');
    }

	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * TODO set return type after upgrade to PHP 7.1
	 */
	public function getDescription(): ?string
	{
		return $this->description;
	}

	/**
	 * TODO set return type after upgrade to PHP 7.1
	 */
	public function getOriginUrl(): ?string
	{
		return $this->originUrl;
	}

	public function getStart(): DateTime
	{
		return $this->start;
	}

	/**
	 * TODO set return type after upgrade to PHP 7.1
	 */
	public function getEnd(): ?DateTime
	{
		return $this->end;
	}

	/**
	 * TODO set return type after upgrade to PHP 7.1
	 */
	public function getImage(): ?string
	{
		return $this->image;
	}

	/**
	 * TODO set return type after upgrade to PHP 7.1
	 */
	public function getRate(): ?int
	{
		return $this->rate;
	}

	public function getCreated(): ?DateTime
    {
        return $this->created;
    }

    public function setRate(int $rate): void
    {
        if ($rate < 1 || $rate > 5) {
            throw new InvalidArgumentException('Rate value must be 1 to 5');
        }
        $this->rate = $rate;
    }

	public static function calculateRateByAttendeesCount(int $count)
	{
		if ($count <= 50) {
			$this->setRate(1);
		} elseif ($count <= 200) {
			$this->setRate(2);
		} elseif ($count <= 500) {
			$this->setRate(3);
		} elseif ($count <= 1000) {
			$this->setRate(4);
		} else {
			$this->setRate(5);
		}
	}
}
