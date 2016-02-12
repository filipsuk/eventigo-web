<?php

namespace App\Components\Newsletter;

use Nette\Database\Table\ActiveRow;


interface NewsletterFactory
{
	/**
	 * @param \Nette\Database\Table\ActiveRow $newsletter
	 * @param array $events
	 * @return \App\Components\Newsletter\Newsletter
	 */
	public function create(ActiveRow $newsletter, array $events);
}