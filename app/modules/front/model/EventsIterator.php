<?php

namespace App\Modules\Front\Model;

use App\Modules\Core\Model\Iterator;


class EventsIterator extends Iterator
{
	private $thisWeek = FALSE;

	private $thisMonth = FALSE;

	private $nextMonth = FALSE;

	private $upcoming = FALSE;


	/**
	 * @return bool
	 */
	public function drawThisWeekTitle()
	{
		if (!$this->thisWeek && $this->current()->thisWeek) {
			return $this->thisWeek = TRUE;
		} else {
			return FALSE;
		}
	}


	/**
	 * @return bool
	 */
	public function drawThisMonthTitle()
	{
		if (!$this->thisMonth && $this->current()->thisMonth && !$this->current()->thisWeek) {
			return $this->thisMonth = TRUE;
		} else {
			return FALSE;
		}
	}


	/**
	 * @return bool
	 */
	public function drawNextMonthTitle()
	{
		if (!$this->nextMonth && $this->current()->nextMonth && !$this->current()->thisWeek && !$this->current()->thisMonth) {
			return $this->nextMonth = TRUE;
		} else {
			return FALSE;
		}
	}


	/**
	 * @return bool
	 */
	public function drawUpcomingTitle()
	{
		if (!$this->upcoming && !$this->current()->thisWeek && !$this->current()->thisMonth && !$this->current()->nextMonth) {
			return $this->upcoming = TRUE;
		} else {
			return FALSE;
		}
	}
}