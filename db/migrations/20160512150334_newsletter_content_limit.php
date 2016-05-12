<?php

use Phinx\Migration\AbstractMigration;

class NewsletterContentLimit extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $users = $this->table('users_newsletters');
        $users->changeColumn('content', 'text', array('limit' => \Phinx\Db\Adapter\MysqlAdapter::TEXT_MEDIUM))
            ->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $users = $this->table('users_newsletters');
        $users->changeColumn('content', 'text', array('limit' => \Phinx\Db\Adapter\MysqlAdapter::TEXT_REGULAR))
            ->save();
    }
}
