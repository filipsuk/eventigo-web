<?php

use Phinx\Migration\AbstractMigration;

class UserNewsletterHtml extends AbstractMigration
{
	public function change()
	{
		$this->table('users_newsletters')
			->addColumn('html', 'text', ['after' => 'variables'])
			->update();
	}
}
