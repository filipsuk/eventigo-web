<?php

namespace App\Modules\Front\Components\Settings;


interface SettingsFactory
{
	/**
	 * @return Settings
	 */
	public function create();
}
