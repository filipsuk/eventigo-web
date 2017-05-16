<?php declare(strict_types=1);

namespace App\Modules\Front\Components\SubscriptionTags;

interface SubscriptionTagsFactoryInterface
{
    public function create(): SubscriptionTags;
}
