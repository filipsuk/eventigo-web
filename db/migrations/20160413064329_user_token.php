<?php

use Phinx\Migration\AbstractMigration;

class UserToken extends AbstractMigration
{
	public function change()
	{
		$this->table('users')
			->addColumn('token', 'string', ['limit' => 64, 'after' => 'password', 'null' => true])
			->update();
	}
}
