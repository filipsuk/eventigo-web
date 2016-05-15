<?php

use Phinx\Migration\AbstractMigration;

class UsersEventsSeriesAndOrganisers extends AbstractMigration
{
	public function change()
	{
		// Users follow events series
		$this->table('users_events_series', ['id' => false, 'primary_key' => ['user_id', 'event_series_id']])
			->addColumn('user_id', 'integer', ['signed' => false])
			->addColumn('event_series_id', 'integer', ['signed' => false])
			->addForeignKey('user_id', 'users', 'id', ['update' => 'CASCADE', 'delete' => 'CASCADE'])
			->addForeignKey('event_series_id', 'events_series', 'id', ['update' => 'CASCADE', 'delete' => 'CASCADE'])
			->create();

		// Users follow organisers
		$this->table('users_organisers', ['id' => false, 'primary_key' => ['user_id', 'organiser_id']])
			->addColumn('user_id', 'integer', ['signed' => false])
			->addColumn('organiser_id', 'integer', ['signed' => false])
			->addForeignKey('user_id', 'users', 'id', ['update' => 'CASCADE', 'delete' => 'CASCADE'])
			->addForeignKey('organiser_id', 'organisers', 'id', ['update' => 'CASCADE', 'delete' => 'CASCADE'])
			->create();
	}
}
