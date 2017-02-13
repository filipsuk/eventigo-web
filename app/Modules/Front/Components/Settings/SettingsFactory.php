<?php declare(strict_types=1);

namespace App\Modules\Front\Components\Settings;


interface SettingsFactory
{
	/**
	 * @return Settings
	 */
	public function create();
}
