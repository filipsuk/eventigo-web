<?php declare(strict_types=1);

namespace App\Modules\Front\Components\Settings;

interface SettingsFactoryInterface
{
    public function create(): Settings;
}
