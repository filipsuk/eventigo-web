<?php declare(strict_types=1);

namespace App\Modules\Admin\Components\SignIn;


interface SignInFormFactory
{
	/**
	 * @return SignInForm
	 */
	public function create();
}