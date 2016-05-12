<?php
/**
 * Created by PhpStorm.
 * User: filipsuk
 * Date: 12.05.16
 * Time: 20:26
 */

namespace App\Modules\Core\Model\EventSources;


use App\Modules\Core\Model\Entity\Event;

interface IEventSource
{
	/**
	 * Get event by platform specific ID
	 * 
	 * @param $id
	 * @return Event
	 */
	public function getEventById($id) : Event;
	
}
