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
	public function getSubject()
	{
		return $this->subject;
	}

	/**
	 * @return string
	 */
	public function getFrom()
	{
		return $this->from;
	}

	/**
	 * @return string
	 */
	public function getIntroText()
	{
		return $this->introText;
	}

	/**
	 * @return string
	 */
	public function getOutroText()
	{
		return $this->outroText;
	}

	/**
	 * @return string
	 */
	public function getAuthor()
	{
		return $this->author;
	}

	/**
	 * @return DateTime
	 */
	public function getCreated()
	{
		return $this->created;
	}

	/**
	 * @param string $subject
	 */
	public function setSubject(string $subject)
	{
		$this->subject = $subject;
	}

	/**
	 * @param string $from
	 */
	public function setFrom(string $from)
	{
		$this->from = $from;
	}

	/**
	 * @param string $introText
	 */
	public function setIntroText(string $introText)
	{
		$this->introText = $introText;
	}

	/**
	 * @param string $outroText
	 */
	public function setOutroText(string $outroText)
	{
		$this->outroText = $outroText;
	}

	/**
	 * @param string $author
	 */
	public function setAuthor(string $author)
	{
		$this->author = $author;
	}

}
