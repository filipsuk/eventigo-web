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
		if (!$this->thisWeek && $this->current()->thisWeek) {
			return $this->thisWeek = TRUE;
		} else {
			return FALSE;
		}
	}


	public function drawThisMonthTitle() : bool
	{
		if (!$this->thisMonth && $this->current()->thisMonth && !$this->current()->thisWeek) {
			return $this->thisMonth = TRUE;
		} else {
			return FALSE;
		}
	}


	public function drawNextMonthTitle() : bool
	{
		if (!$this->nextMonth && $this->current()->nextMonth && !$this->current()->thisWeek && !$this->current()->thisMonth) {
			return $this->nextMonth = TRUE;
		} else {
			return FALSE;
		}
	}


	public function drawUpcomingTitle() : bool
	{
		if (!$this->upcoming && !$this->current()->thisWeek && !$this->current()->thisMonth && !$this->current()->nextMonth) {
			return $this->upcoming = TRUE;
		} else {
			return FALSE;
		}
	}
}