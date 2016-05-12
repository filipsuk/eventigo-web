<?php

use Phinx\Migration\AbstractMigration;

/**
 * Prevent event duplicates
 */

class EventsUniqueIndex extends AbstractMigration
{

    public function up()
    {
        $events = $this->table('events');
        $events->changeColumn('name', 'string', array('limit' => 128))
            ->addIndex(array('name', 'start'), array('unique' => true))
            ->save();
    }
    
    public function down()
    {
        $events = $this->table('events');
        $events->changeColumn('name', 'string', array('limit' => 1024))
            ->removeIndex(array('name', 'start'))
            ->save();
    }
}
