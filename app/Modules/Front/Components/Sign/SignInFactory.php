<?php

namespace App\Modules\Front\Components\Sign;


interface SignInFactory
{
	/**
	 * @return SignIn
	 */
	public function create();
}