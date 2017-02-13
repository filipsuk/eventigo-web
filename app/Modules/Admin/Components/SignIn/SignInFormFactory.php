<?php

namespace App\Modules\Admin\Components\SignIn;


interface SignInFormFactory
{
	/**
	 * @return SignInForm
	 */
	public function create();
}