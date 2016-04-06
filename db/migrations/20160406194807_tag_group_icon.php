<?php

use Phinx\Migration\AbstractMigration;

class TagGroupIcon extends AbstractMigration
{
    public function change()
    {
        $this->table('tags_groups')
            ->addColumn('icon', 'string', ['limit' => 255, 'null' => true, 'after' => 'name'])
            ->update();
    }
}
