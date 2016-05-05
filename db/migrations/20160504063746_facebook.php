<?php

use Phinx\Migration\AbstractMigration;

class Facebook extends AbstractMigration
{
    public function change()
    {
        $this->table('users')
            ->addColumn('facebook_id', 'string', ['null' => true, 'after' => 'email', 'limit' => 17])
            ->addColumn('facebook_token', 'string', ['null' => true, 'after' => 'token', 'limit' => 168])
            ->addColumn('firstname', 'string', ['null' => true, 'after' => 'email', 'limit' => 32])
            ->addColumn('fullname', 'string', ['null' => true, 'after' => 'firstname', 'limit' => 122])
            ->changeColumn('email', 'string', ['limit' => 254, 'null' => true])
            ->update();
    }
}
