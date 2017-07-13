<?php

use Phinx\Migration\AbstractMigration;

class Venue extends AbstractMigration
{
    public function change()
    {
        $this->table('events')
            ->addColumn(
                'venue',
                Phinx\Db\Adapter\MysqlAdapter::PHINX_TYPE_STRING,
                ['after' => 'end', 'null' => true])
            ->addColumn(
                'country_id',
                Phinx\Db\Adapter\MysqlAdapter::PHINX_TYPE_STRING,
                ['after' => 'venue', 'limit' => 2, 'null' => true]
            )
            ->addForeignKey('country_id', 'countries', 'id')
            ->update();
    }
}
