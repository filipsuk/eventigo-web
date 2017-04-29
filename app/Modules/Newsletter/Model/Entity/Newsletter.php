<?php declare(strict_types=1);

namespace App\Modules\Newsletter\Model\Entity;

use Nette\Utils\DateTime;

class Newsletter
{
	/**
	 * @var int
	 */
	private $id;

	/**
	 * @var string
	 */
	private $subject;

	/**
	 * @var string
	 */
	private $from;

	/**
	 * @var string
	 */
	private $introText;

	/**
	 * @var string
	 */
	private $outroText;

	/**
	 * @var string
	 */
	private $author;

	/**
	 * @var DateTime
	 */
	private $created;

	public function getId(): int
	{
		return $this->id;
	}

	public function getSubject(): string
	{
		return $this->subject;
	}

	public function getFrom(): string
	{
		return $this->from;
	}

	public function getIntroText(): string
	{
		return $this->introText;
	}

	public function getOutroText(): string
	{
		return $this->outroText;
	}

	public function getAuthor(): string
	{
		return $this->author;
	}

	public function getCreated(): DateTime
	{
		return $this->created;
	}

	public function setSubject(string $subject): void
	{
		$this->subject = $subject;
	}

	public function setFrom(string $from): void
	{
		$this->from = $from;
	}

	public function setIntroText(string $introText): void
	{
		$this->introText = $introText;
	}

	public function setOutroText(string $outroText): void
	{
		$this->outroText = $outroText;
	}

	public function setAuthor(string $author): void
	{
		$this->author = $author;
	}
}
