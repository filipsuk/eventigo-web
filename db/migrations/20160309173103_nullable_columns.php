<?php

use Phinx\Migration\AbstractMigration;

class NullableColumns extends AbstractMigration
{
    public function change()
    {
        $this->table('events')
            ->changeColumn('description', 'text', ['null' => true])
            ->changeColumn('end', 'datetime', ['null' => true])
            ->changeColumn('image', 'string', ['limit' => 2083, 'comment' => 'url', 'null' => true])
            ->update();
    }
}
