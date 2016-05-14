<?php

use Phinx\Migration\AbstractMigration;

class EventState extends AbstractMigration
{
	public function change()
	{
		$this->table('events')
			->addColumn('state', 'enum', ['values' => ['approved', 'not-approved', 'skip'],
				'default' => 'not-approved', 'after' => 'rate'])
			->update();

		$this->execute('UPDATE events SET state = "approved"');
	}
}
