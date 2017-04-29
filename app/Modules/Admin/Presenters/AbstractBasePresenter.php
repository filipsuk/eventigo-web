<?php declare(strict_types=1);

namespace App\Modules\Admin\Presenters;

use App\Modules\Core\Presenters\AbstractBasePresenter as CoreAbstractBasePresenter;

abstract class AbstractBasePresenter extends CoreAbstractBasePresenter
{
	protected function startup(): void
	{
		parent::startup();

		// @todo: use event subscriber
		if (! $this->user->isLoggedIn() || ! $this->user->isInRole('admin')) {
			$this->redirect('Sign:in');
		}
	}
}
