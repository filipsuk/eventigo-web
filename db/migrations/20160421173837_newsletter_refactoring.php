<?php

use Phinx\Migration\AbstractMigration;

class NewsletterRefactoring extends AbstractMigration
{
	public function change()
	{
		$this->table('users_newsletters')->drop();
		$this->table('newsletters')->drop();
		$this->table('newsletters_contents')->drop();
		$this->table('newsletters_layouts')->drop();

		$this->table('users_newsletters', ['id' => false, 'primary_key' => 'id'])
			->addColumn('id', 'integer', ['signed' => false, 'identity' => true])
			->addColumn('from', 'string', ['length' => 254])
			->addColumn('subject', 'string', ['length' => 100])
			->addColumn('content', 'text')
			->addColumn('hash', 'string', ['length' => 32, 'null' => true])
			->addColumn('user_id', 'integer', ['signed' => false])
			->addColumn('sent', 'datetime', ['null' => true])
			->addColumn('created', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
			->addForeignKey('user_id', 'users', 'id', ['update' => 'CASCADE', 'delete' => 'CASCADE'])
			->create();

		$this->table('users')
			->addColumn('newsletter', 'boolean', ['after' => 'token', 'default' => true])
			->update();
	}
}
