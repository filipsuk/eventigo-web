<?php

use Phinx\Migration\AbstractMigration;

class EventRedirect extends AbstractMigration
{
    public function change()
    {
        $this->table('events_redirects')
            ->addColumn('event_id', 'integer', ['signed' => false])
            ->addColumn('user_id', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('created', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addForeignKey('event_id', 'events', 'id', ['update' => 'CASCADE', 'delete' => 'CASCADE'])
            ->addForeignKey('user_id', 'users', 'id', ['update' => 'CASCADE', 'delete' => 'CASCADE'])
            ->create();
    }
}
