<?php

use Phinx\Migration\AbstractMigration;

final class DemoUser extends AbstractMigration
{
    public function change()
    {
		$demoUser = [
			'email' => 'demo@gmail.com',
			'password' => '$2y$10$00l9Qn.Ou7xaiHgT0fJpHuwbC3mbjYqQF4SdV3H616W4nKoagwhdy', // demo
			'newsletter' => true
		];

		$this->insert('users', $demoUser);
    }
}
