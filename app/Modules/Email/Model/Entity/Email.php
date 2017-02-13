<?php

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

	/**
	 * @return string
	 */
	public function getFrom()
	{
		return $this->from;
	}

	/**
	 * @param string $from
	 * @return Email
	 */
	public function setFrom($from)
	{
		$this->from = $from;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTo()
	{
		return $this->to;
	}

	/**
	 * @param string $to
	 * @return Email
	 */
	public function setTo($to)
	{
		$this->to = $to;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSubject()
	{
		return $this->subject;
	}

	/**
	 * @param string $subject
	 * @return Email
	 */
	public function setSubject($subject)
	{
		$this->subject = $subject;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getBody()
	{
		return $this->body;
	}

	/**
	 * @param string $body
	 * @return Email
	 */
	public function setBody($body)
	{
		$this->body = $body;
		return $this;
	}
	
	
}
