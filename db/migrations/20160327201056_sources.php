<?php

use Phinx\Migration\AbstractMigration;

class Sources extends AbstractMigration
{
    public function change()
    {
        $this->table('sources')
            ->addColumn('name', 'string', ['limit' => 1024, 'null' => true])
            ->addColumn('url', 'string', ['limit' => 2083, 'null' => true])
            ->addColumn('event_series_id', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('check_frequency', 'integer', ['default' => 1])
            ->addColumn('next_check', 'date')
            ->addColumn('created', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addForeignKey('event_series_id', 'events_series', 'id', ['update' => 'CASCADE', 'delete' => 'CASCADE'])
            ->create();
    }
}
