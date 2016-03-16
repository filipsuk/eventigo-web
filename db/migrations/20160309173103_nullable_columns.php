<?php

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

class NullableColumns extends AbstractMigration
{
    public function change()
    {
        //ALTER TABLE ygpnaptynr.events MODIFY image VARCHAR(2083) COMMENT 'url';
        $this->table('events')
            ->changeColumn('description', 'text', ['null' => true])
            ->changeColumn('end', 'datetime', ['null' => true])
            ->changeColumn('image', 'string', ['limit' => 2083, 'comment' => 'url', 'null' => true])
            ->update();
    }
}
