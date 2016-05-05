<?php

use Phinx\Migration\AbstractMigration;

class UserLastAccess extends AbstractMigration
{
    public function change()
    {
        $this->table('users')
            ->addColumn('last_access', 'datetime', ['null' => true, 'after' => 'newsletter'])
            ->update();
    }
}
