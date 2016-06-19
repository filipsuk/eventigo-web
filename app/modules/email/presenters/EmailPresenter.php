<?php

namespace App\Modules\Email\Presenters;

use App\Modules\Core\Model\UserModel;
use App\Modules\Core\Presenters\BasePresenter;


class EmailPresenter extends BasePresenter
{
	/** @var UserModel @inject */
	public $userModel;

	public function renderLogin($token)
	{
		$this->template->token = $token;
	}

}
