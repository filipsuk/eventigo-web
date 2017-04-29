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

	public function getIntroText(): string
	{
		return $this->introText;
	}

	public function setIntroText(string $introText): BasicEmail
	{
		$this->introText = $introText;
		return $this;
	}

	public function getButtonText(): string
	{
		return $this->buttonText;
	}

	public function setButtonText(string $buttonText): self
	{
		$this->buttonText = $buttonText;
		return $this;
	}

	public function getButtonUrl(): Url
	{
		return $this->buttonUrl;
	}

	public function setButtonUrl(Url $buttonUrl): self
	{
		$this->buttonUrl = $buttonUrl;
		return $this;
	}

	public function getFooterText(): string
	{
		return $this->footerText;
	}

	public function setFooterText(string $footerText): BasicEmail
	{
		$this->footerText = $footerText;
		return $this;
	}
}
