<?php

namespace App\Model;


class EventsIterator extends Iterator
{
	private $thisWeek = FALSE;

	private $thisMonth = FALSE;

	private $nextMonth = FALSE;

	private $upcoming = FALSE;


	public function drawThisWeekTitle() : bool
	{
		if (!$this->thisWeek && $this->last()->thisWeek) {
			return $this->thisWeek = TRUE;
		} else {
			return FALSE;
		}
	}


	public function drawThisMonthTitle() : bool
	{
		if (!$this->thisMonth && $this->last()->thisMonth && !$this->last()->thisWeek) {
			return $this->thisMonth = TRUE;
		} else {
			return FALSE;
		}
	}


	public function drawNextMonthTitle() : bool
	{
		if (!$this->nextMonth && $this->last()->nextMonth && !$this->last()->thisWeek && !$this->last()->thisMonth) {
			return $this->nextMonth = TRUE;
		} else {
			return FALSE;
		}
	}


	public function drawUpcomingTitle() : bool
	{
		if (!$this->upcoming && !$this->last()->thisWeek && !$this->last()->thisMonth && !$this->last()->nextMonth) {
			return $this->upcoming = TRUE;
		} else {
			return FALSE;
		}
	}
}