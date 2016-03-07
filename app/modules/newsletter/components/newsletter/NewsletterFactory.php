<?php

namespace App\Modules\Newsletter\Components\Newsletter;

use Nette\Database\Table\ActiveRow;


interface NewsletterFactory
{
	/**
	 * @param \Nette\Database\Table\ActiveRow $newsletter
	 * @param array $events
	 * @return Newsletter
	 */
	public function create(ActiveRow $newsletter, array $events);
}