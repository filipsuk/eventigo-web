<?php declare(strict_types=1);

namespace App\Modules\Admin\Components\SignIn;


interface SignInFormFactory
{
	public function create(): SignInForm;
}
