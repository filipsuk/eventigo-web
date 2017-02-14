<?php declare(strict_types=1);

namespace App\Modules\Email\Model\Entity;

/**
 * Class Email represents data structure of general email. 
 * Serves as a parent to different email types sent to users.
 * 
 * @package App\Modules\Email\Model\Entity
 */
class Email
{
	/**
	 * @var string From email address
	 */
	private $from;

	/**
	 * @var string To email address
	 */
	private $to;

	/**
	 * @var string Subject of email
	 */
	private $subject;

	/**
	 * @var string HTML body
	 */
	private $body;

	public function getFrom(): string
	{
		return $this->from;
	}

	public function setFrom(string $from): self
	{
		$this->from = $from;
		return $this;
	}

	public function getTo(): string
	{
		return $this->to;
	}

	public function setTo(string $to): self
	{
		$this->to = $to;
		return $this;
	}

	public function getSubject(): string
	{
		return $this->subject;
	}

	public function setSubject(string $subject): self
	{
		$this->subject = $subject;
		return $this;
	}

	public function getBody(): string
	{
		return $this->body;
	}

	public function setBody(string $body): self
	{
		$this->body = $body;
		return $this;
	}
}
