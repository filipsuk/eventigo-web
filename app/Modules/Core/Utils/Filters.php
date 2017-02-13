<?php declare(strict_types=1);

namespace App\Modules\Core\Utils;

use Nette\Security\Identity;


class Filters
{
	/** @var \Kdyby\Translation\Translator */
	public static $translator;

	/**
	 * @param \Kdyby\Translation\Translator $translator
	 */
	public static function setTranslator(\Kdyby\Translation\Translator $translator)
	{
		self::$translator = $translator;
	}


	public static function loader(string $helper)
	{
		if (method_exists(__CLASS__, $helper)) {
			return call_user_func_array(__CLASS__ . '::' . $helper, array_slice(func_get_args(), 1));
		}
	}


	/**
	 * @param \Nette\Utils\DateTime $a
	 * @param \Nette\Utils\DateTime|null $b
	 * @return string
	 */
	public static function datetime(\Nette\Utils\DateTime $a, \Nette\Utils\DateTime $b = null) : string
	{
		\App\Modules\Core\Utils\DateTime::setTranslator(self::$translator);
		return \App\Modules\Core\Utils\DateTime::eventsDatetimeFilter($a, $b);
	}

	/**
	 * @param Identity $identity
	 * @return string
	 */
	public static function username(Identity $identity) : string
	{
		return $identity->fullname ?: $identity->email ?: self::$translator->translate('front.nav.user');
	}

	/**
	 * Inline filter used for CSS inline and UTF8 to HTML entities conversion (email clients compatibility)
	 * @param string $s
	 * @param bool $stripTags
	 * @return string
	 */
	public static function inline(string $s, bool $stripTags = true) : string
	{
		$output = Helper::utfToHtmlEntities($s);
		if ($stripTags) {
			$output = strip_tags($output);
		}
		return $output;
	}
}
