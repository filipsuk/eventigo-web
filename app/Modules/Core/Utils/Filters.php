<?php declare(strict_types=1);

namespace App\Modules\Core\Utils;

use App\Modules\Core\Utils\DateTime as EventigoDateTime;
use Kdyby\Translation\Translator;
use Nette\Security\Identity;
use Nette\Utils\DateTime;


final class Filters
{
	/**
	 * @var Translator
	 * */
	public static $translator;

	public static function setTranslator(Translator $translator): void
	{
		self::$translator = $translator;
	}


	/**
	 * @return mixed|void
	 */
	public static function loader(string $helper)
	{
		if (method_exists(__CLASS__, $helper)) {
			return call_user_func_array(__CLASS__ . '::' . $helper, array_slice(func_get_args(), 1));
		}
	}


	public static function datetime(DateTime $a, ?DateTime $b = null): string
	{
		EventigoDateTime::setTranslator(self::$translator);
		return EventigoDateTime::eventsDatetimeFilter($a, $b);
	}

	public static function username(Identity $identity): string
	{
		return $identity->fullname ?: $identity->email ?: self::$translator->translate('front.nav.user');
	}

	/**
	 * Inline filter used for CSS inline and UTF8 to HTML entities conversion (email clients compatibility)
	 */
	public static function inline(string $s, bool $stripTags = true): string
	{
		$output = Helper::utfToHtmlEntities($s);
		if ($stripTags) {
			$output = strip_tags($output);
		}
		return $output;
	}
}
