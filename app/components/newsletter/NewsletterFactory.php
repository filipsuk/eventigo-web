<?php

namespace App\Components\Newsletter;


interface NewsletterFactory
{
	/**
	 * @param array $events
	 * @return \App\Components\Newsletter\Newsletter
	 */
	public function create(array $events);
}