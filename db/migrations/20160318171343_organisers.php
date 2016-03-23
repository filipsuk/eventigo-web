<?php

use Phinx\Migration\AbstractMigration;

class Organisers extends AbstractMigration
{
	public function change()
	{
		// Create organisers
		$this->table('organisers', ['id' => false, 'primary_key' => 'id'])
			->addColumn('id', 'integer', ['signed' => false, 'identity' => true])
			->addColumn('name', 'string', ['limit' => 255])
			->addColumn('url', 'string', ['limit' => 2083, 'null' => true])
			->addColumn('icon', 'string', ['limit' => 2083, 'null' => true])
			->addColumn('image', 'string', ['limit' => 2083, 'null' => true])
			->addColumn('updated', 'timestamp', ['null' => true, 'default' => null, 'update' => 'CURRENT_TIMESTAMP'])
			->addColumn('created', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
			->addIndex('name')
			->create();

		// Create series
		$this->table('events_series', ['id' => false, 'primary_key' => 'id'])
			->addColumn('id', 'integer', ['signed' => false, 'identity' => true])
			->addColumn('name', 'string', ['limit' => 255, 'null' => true])
			->addColumn('url', 'string', ['limit' => 2083, 'null' => true])
			->addColumn('icon', 'string', ['limit' => 2083, 'null' => true])
			->addColumn('image', 'string', ['limit' => 2083, 'null' => true])
			->addColumn('organiser_id', 'integer', ['signed' => false])
			->addForeignKey('organiser_id', 'organisers', 'id', ['update' => 'CASCADE'])
			->create();

		// Add foreign key for events
		$this->table('events')
			->addColumn('event_series_id', 'integer', ['after' => 'rate', 'null' => true, 'signed' => false])
			->addForeignKey('event_series_id', 'events_series', 'id', ['update' => 'CASCADE'])
			->update();
	}
}
