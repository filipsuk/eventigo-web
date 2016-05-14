<?php

use Phinx\Migration\AbstractMigration;

class EventApproved extends AbstractMigration
{
	public function change()
	{
		$this->table('events')
			->addColumn('approved', 'datetime', ['after' => 'state', 'null' => true])
			->update();

		$this->execute("UPDATE events SET approved = created");
	}
}
