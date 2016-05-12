<?php
/**
 * Created by PhpStorm.
 * User: filipsuk
 * Date: 12.05.16
 * Time: 20:31
 */

namespace App\Modules\Core\Model\Entity;


use Nette\Object;
use Nette\Utils\DateTime;

class Event extends Object
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

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 * @return Event
	 */
	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @param string $description
	 * @return Event
	 */
	public function setDescription($description)
	{
		$this->description = $description;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getOriginUrl()
	{
		return $this->originUrl;
	}

	/**
	 * @param string $originUrl
	 * @return Event
	 */
	public function setOriginUrl($originUrl)
	{
		$this->originUrl = $originUrl;
		return $this;
	}

	/**
	 * @return DateTime
	 */
	public function getStart()
	{
		return $this->start;
	}

	/**
	 * @param DateTime $start
	 * @return Event
	 */
	public function setStart($start)
	{
		$this->start = $start;
		return $this;
	}

	/**
	 * @return DateTime
	 */
	public function getEnd()
	{
		return $this->end;
	}

	/**
	 * @param DateTime $end
	 * @return Event
	 */
	public function setEnd($end)
	{
		$this->end = $end;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getImage()
	{
		return $this->image;
	}

	/**
	 * @param string $image Image URL
	 * @return Event
	 */
	public function setImage($image)
	{
		$this->image = $image;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getRate()
	{
		return $this->rate;
	}

	/**
	 * @param int $rate Event Size (1-5)
	 * @return Event
	 * @throws \InvalidArgumentException
	 */
	public function setRate($rate)
	{
		if ($rate < 1 || $rate > 5) {
			throw new \InvalidArgumentException('Rate value must be 1 to 5');
		}
		$this->rate = $rate;
		return $this;
	}

	/**
	 * Set event rate by number of attendees
	 * 
	 * @param int $count
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
