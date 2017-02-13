<?php declare(strict_types=1);

namespace App\Modules\Front\Components\Sign;


interface SignInFactory
{
	/**
	 * @return SignIn
	 */
	public function create();
}