<?php

namespace App\Modules\Newsletter\Model;

use App\Modules\Core\Model\BaseModel;


class NewsletterModel extends BaseModel
{
	const TABLE_NAME = 'newsletters'; // TODO migrace

	/**
	 * Get latest newsletter texts
	 * 
	 * @return array
	 */
	public function getLatest() : array 
	{
		//TODO build migrations and get from database
		//TODO update instrukci v readme
		return [
			'subject' => 'Akce na příští týden',
			'from' => 'filip@eventigo.cz',
			'introText' => 'Ahoj, <br/> lorem ipsum.',
			'outroText' => 'To je pro tento t&#xFD;den v&#x161;echno. Jsme r&#xE1;di,
				&#x17E;e eventigo pou&#x17E;&#xED;v&#xE1;&#x161; a budeme r&#xE1;di
				za jak&#xFD;koliv feedback odpov&#x11B;d&#xED; na tento email.
				Tvoje n&#xE1;vrhy s chut&#xED; implementujeme (&#x30C4;).',
			'author' => 'Filip'
		];
	}
}
