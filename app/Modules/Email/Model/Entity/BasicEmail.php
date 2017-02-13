<?php declare(strict_types=1);

namespace App\Modules\Email\Model\Entity;


use Nette\Http\Url;

/**
 * Class BasicEmail extends Email for use in transactional emails with intro text, link button and footer text.
 * 
 * @package App\Modules\Email\Model\Entity
 */
class BasicEmail extends Email
{
	/**
	 * @var string
	 */
	private $introText;

	/**
	 * @var string
	 */
	private $buttonText;

	/**
	 * @var Url
	 */
	private $buttonUrl;

	/**
	 * @var string
	 */
	private $footerText;

	/**
	 * @return string
	 */
	public function getIntroText()
	{
		return $this->introText;
	}

	/**
	 * @param string $introText
	 * @return BasicEmail
	 */
	public function setIntroText($introText)
	{
		$this->introText = $introText;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getButtonText()
	{
		return $this->buttonText;
	}

	/**
	 * @param string $buttonText
	 * @return BasicEmail
	 */
	public function setButtonText($buttonText)
	{
		$this->buttonText = $buttonText;
		return $this;
	}

	/**
	 * @return Url
	 */
	public function getButtonUrl()
	{
		return $this->buttonUrl;
	}

	/**
	 * @param Url $buttonUrl
	 * @return BasicEmail
	 */
	public function setButtonUrl($buttonUrl)
	{
		$this->buttonUrl = $buttonUrl;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getFooterText()
	{
		return $this->footerText;
	}

	/**
	 * @param string $footerText
	 * @return BasicEmail
	 */
	public function setFooterText($footerText)
	{
		$this->footerText = $footerText;
		return $this;
	}
}
