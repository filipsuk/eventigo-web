<?php declare(strict_types=1);

namespace App\Modules\Core\Presenters;

use Nette\Application\BadRequestException;
use Nette\Application\Request;

final class Error4xxPresenter extends AbstractBasePresenter
{
    public function startup(): void
    {
        parent::startup();
        if (! $this->getRequest()->isMethod(Request::FORWARD)) {
            $this->error();
        }
    }

    public function renderDefault(BadRequestException $exception): void
    {
        // load template 403.latte or 404.latte or ... 4xx.latte
        $file = __DIR__ . "/templates/Error/{$exception->getCode()}.latte";
        $this->template->setFile(is_file($file) ? $file : __DIR__ . '/templates/Error/4xx.latte');
    }
}
