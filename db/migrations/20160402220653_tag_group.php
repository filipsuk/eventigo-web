<?php

use Phinx\Migration\AbstractMigration;

class TagGroup extends AbstractMigration
{
    public function change()
    {
        $this->table('tags_groups', ['id' => false, 'primary_key' => 'id'])
            ->addColumn('id', 'integer', ['signed' => false, 'identity' => true])
            ->addColumn('name', 'string', ['limit' => 32])
            ->addColumn('created', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->create();

        $this->table('tags')
            ->addColumn('tag_group_id', 'integer', ['signed' => false, 'null' => true, 'after' => 'code'])
            ->addForeignKey('tag_group_id', 'tags_groups', 'id', ['update' => 'CASCADE', 'delete' => 'SET_NULL'])
            ->update();

        $this->table('tags_groups')->insert([
            ['name' => 'development'],
            ['name' => 'business'],
            ['name' => 'marketing'],
            ['name' => 'others'],
        ])->update();
    }
}
