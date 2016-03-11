<?php

namespace App\Modules\Admin\Components;


interface SignInFormFactory
{
	/**
	 * @return SignInForm
	 */
	public function create();
}