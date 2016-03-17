<?php

use Phinx\Migration\AbstractMigration;

class PasswordColumn extends AbstractMigration
{
    public function change()
    {
        $this->table('users')
            ->addColumn('password', 'string', ['limit' => 152, 'after' => 'email', 'null' => true])
            ->update();
    }
}
