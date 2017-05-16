<?php declare(strict_types=1);

namespace App\Modules\Core\Utils;

use DateTimeInterface;
use Kdyby\Translation\Translator;
use Nette\Utils\DateTime as NetteDateTime;

final class DateTime
{
    /**
     * @var string
     */
    public const DATETIME_FORMAT = 'd. m. Y H:i';

    /**
     * @var string
     */
    public const DATE_FORMAT = 'd. m. Y';

    /**
     * @var string
     */
    public const NO_ZERO_DATE_FORMAT = 'j. n. Y';

    /**
     * @var string
     */
    public const W3C_DATE = 'Y-m-d';

    /**
     * @var string
     */
    public const W3C_DATETIME_MINUTES = 'Y-m-d H:i';

    /**
     * @var string
     */
    public const W3C_DATETIME = 'Y-m-d H:i:s';

    /**
     * @var string
     */
    public const TIME_MINUTES = 'H:i';

    /**
     * @var \Kdyby\Translation\Translator
     */
    public static $translator;

    /**
     * Get maximum of given datetimes.
     *
     * @param NetteDateTime[]|DateTimeInterface[]|null[] $dateTimes
     * @return NetteDateTime|DateTimeInterface
     */
    public static function max(...$dateTimes)
    {
        $dateTimes = array_filter($dateTimes); // filter out NULLs

        $max = reset($dateTimes) ?: null;
        foreach ($dateTimes as $dateTime) {
            if ($max < $dateTime) {
                $max = $dateTime;
            }
        }

        return $max;
    }

    public static function setTranslator(Translator $t): void
    {
        self::$translator = $t;
    }

    public static function eventsDatetimeFilter(NetteDateTime $a, ?NetteDateTime $b = null): string
    {
        // Translate name of day
        $aDayName = self::$translator->translate('front.datetime.' . strtolower(strftime('%A', $a->getTimestamp())));

        if ($b && ($a->format('dmy') !== $b->format('dmy'))) {
            // Two day event
            $result = $aDayName . $a->format(' j. n. ') . '&nbsp;&ndash;&nbsp;' . $b->format('j. n. Y');
        } else {
            // One day event
            $result = $aDayName . $a->format(' j. n. Y');
            // Add Hour:minute time if its not 00:00
            if ((int) $a->format('G') > 0) {
                $result .= $a->format(' G:i');
            }
        }

        return $result;
    }
}
