<?php

use Phinx\Migration\AbstractMigration;

class EventRedirect extends AbstractMigration
{
    public function change()
    {
        $this->table('events_redirects', ['id' => false, 'primary_key' => ['event_id', 'user_id', 'created']])
            ->addColumn('event_id', 'integer', ['signed' => false])
            ->addColumn('user_id', 'integer', ['signed' => false])
            ->addColumn('created', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addForeignKey('event_id', 'events', 'id', ['update' => 'CASCADE', 'delete' => 'CASCADE'])
            ->addForeignKey('user_id', 'users', 'id', ['update' => 'CASCADE', 'delete' => 'CASCADE'])
            ->create();
    }
}
