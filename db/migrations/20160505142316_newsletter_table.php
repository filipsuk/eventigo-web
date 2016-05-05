<?php

use Phinx\Migration\AbstractMigration;

class NewsletterTable extends AbstractMigration
{
    public function change()
    {
        $newsletter = $this->table('newsletters');
        $newsletter->addColumn('subject', 'string', array('limit' => 100))
            ->addColumn('from', 'string', array('limit' => 100))
            ->addColumn('intro_text', 'text')
            ->addColumn('outro_text', 'text')
            ->addColumn('author', 'string', array('limit' => 20))
            ->addColumn('created', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->create();
    }
}
