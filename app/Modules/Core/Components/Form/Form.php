<?php declare(strict_types=1);

namespace App\Modules\Core\Components\Form;

use Nette\Application\UI\Form as NativeForm;
use Nette\Forms\Rendering\DefaultFormRenderer;

final class Form extends NativeForm
{
    public function __construct()
    {
        parent::__construct();

        /** @var DefaultFormRenderer $renderer */
        $renderer = $this->getRenderer();
        $renderer->wrappers['label']['container'] = 'th class="th-label"';
        $renderer->wrappers['control']['container'] = 'td class="td-control"';
    }
}
