<?php declare(strict_types=1);

namespace App\Modules\Core\Model\Entity;

use InvalidArgumentException;
use Nette\Database\IRow;
use Nette\Utils\DateTime;
use RuntimeException;

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
    private $venue;

    /**
     * @var string
     */
    private $countryCode;

    /**
     * @var string
     */
    private $image;

    /**
     * @var int Size of event
     */
    private $rate;

    /**
     * @var DateTime
     */
    private $created;

    /**
     * @var DateTime
     */
    private $approved;

    public function __construct(
        ?int $id = null,
        string $name,
        ?string $description = null,
        ?string $originUrl = null,
        DateTime $start,
        ?DateTime $end = null,
        ?string $venue = null,
        ?string $countryCode = null,
        ?string $image = null,
        ?int $rate = null,
        ?DateTime $created = null,
        ?DateTime $approved = null
    )
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->originUrl = $originUrl;
        $this->start = $start;
        $this->end = $end;
        $this->venue = $venue;
        $this->countryCode = $countryCode;
        $this->image = $image;
        $this->rate = $rate;
        $this->created = $created;
        $this->approved = $approved;
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
            $eventRow['venue'],
            $eventRow['country_id'],
            $eventRow['image'],
            $eventRow['rate'],
            $eventRow['created'],
            $eventRow['approved']
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

        throw new RuntimeException('Could not calculate hash, "id" or "created" field not set');
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getOriginUrl(): ?string
    {
        return $this->originUrl;
    }

    public function setOriginUrl(string $originUrl): void
    {
        $this->originUrl = $originUrl;
    }

    public function getStart(): DateTime
    {
        return $this->start;
    }

    public function setStart(DateTime $start): void
    {
        $this->start = $start;
    }

    public function getEnd(): ?DateTime
    {
        return $this->end;
    }

    public function setEnd(DateTime $end): void
    {
        $this->end = $end;
    }

    public function getVenue(): ?string
    {
        return $this->venue;
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): void
    {
        $this->image = $image;
    }

    public function getRate(): ?int
    {
        return $this->rate;
    }

    public function getCreated(): ?DateTime
    {
        return $this->created;
    }

    public function getApproved(): ?DateTime
    {
        return $this->approved;
    }

    public function setRate(int $rate): void
    {
        $this->ensureRateIsValid($rate);
        $this->rate = $rate;
    }

    public static function calculateRateByAttendeesCount(int $count): int
    {
        if ($count <= 50) {
            return 1;
        }

        if ($count <= 200) {
            return 2;
        }

        if ($count <= 500) {
            return 3;
        }

        if ($count <= 1000) {
            return 4;
        }

        return 5;
    }

    private function ensureRateIsValid(int $rate): void
    {
        if ($rate < 1 || $rate > 5) {
            throw new InvalidArgumentException('Rate value must be 1 to 5');
        }
    }
}
