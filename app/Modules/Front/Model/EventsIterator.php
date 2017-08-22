<?php declare(strict_types=1);

namespace App\Modules\Front\Model;

use App\Modules\Core\Model\AbstractIterator;
use Countable;

final class EventsIterator extends AbstractIterator implements Countable
{
    /**
     * @var bool
     */
    private $thisWeek = false;

    /**
     * @var bool
     */
    private $thisMonth = false;

    /**
     * @var bool
     */
    private $nextMonth = false;

    /**
     * @var bool
     */
    private $upcoming = false;

    public function drawThisWeekTitle(): bool
    {
        if (! $this->thisWeek && $this->current()->thisWeek) {
            return $this->thisWeek = true;
        }

        return false;
    }

    public function drawThisMonthTitle(): bool
    {
        if (! $this->thisMonth && $this->current()->thisMonth && ! $this->current()->thisWeek) {
            return $this->thisMonth = true;
        }

        return false;
    }

    public function drawNextMonthTitle(): bool
    {
        if (! $this->nextMonth && $this->current()->nextMonth
            && ! $this->current()->thisWeek && ! $this->current()->thisMonth
        ) {
            return $this->nextMonth = true;
        }

        return false;
    }

    public function drawUpcomingTitle(): bool
    {
        if (! $this->upcoming && ! $this->current()->thisWeek
            && ! $this->current()->thisMonth && ! $this->current()->nextMonth
        ) {
            return $this->upcoming = true;
        }

        return false;
    }

    public function count(): int
    {
        return count($this->data);
    }
}
