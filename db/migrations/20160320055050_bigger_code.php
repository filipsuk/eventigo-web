<?php

use Phinx\Migration\AbstractMigration;

class BiggerCode extends AbstractMigration
{
    public function change()
    {
        $this->table('tags')
            ->changeColumn('code', 'string', ['limit' => 32, 'null' => false])
            ->update();
    }
}
