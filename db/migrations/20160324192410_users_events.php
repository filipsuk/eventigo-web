<?php

use Phinx\Migration\AbstractMigration;

class UsersEvents extends AbstractMigration
{
    public function change()
    {
        $this->table('users_events', ['id' => false, 'primary_key' => ['user_id', 'event_id']])
            ->addColumn('user_id', 'integer', ['signed' => false])
            ->addColumn('event_id', 'integer', ['signed' => false])
            ->addColumn('created', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addForeignKey('user_id', 'users', 'id', ['update' => 'CASCADE', 'delete' => 'CASCADE'])
            ->addForeignKey('event_id', 'events', 'id', ['update' => 'CASCADE', 'delete' => 'CASCADE'])
            ->create();
    }
}
