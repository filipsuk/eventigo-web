<?php

namespace App\Modules\Admin\Presenters;

use Nette;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends \App\Modules\Core\Presenters\BasePresenter
{
	protected function startup()
	{
		parent::startup();

		if (!$this->getUser()->isLoggedIn() && !$this->getUser()->isInRole('admin')) {
			$this->redirect('Sign:in');
		}
	}
}
