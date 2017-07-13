<?php

use Phinx\Migration\AbstractMigration;

class AbroadEvents extends AbstractMigration
{
    public function change()
    {
        $this->table('users')
            ->addColumn(
                'abroad_events',
                Phinx\Db\Adapter\MysqlAdapter::PHINX_TYPE_BOOLEAN,
                ['after' => 'newsletter', 'default' => true]
            )
            ->update();
    }
}
