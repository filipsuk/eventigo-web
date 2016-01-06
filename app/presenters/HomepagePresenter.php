<?php

namespace App\Presenters;

use Nette;


class HomepagePresenter extends BasePresenter
{

	public function renderDefault()
	{
		$this->template->tags = [
			['code' => 'php', 'name' => 'PHP'],
			['code' => 'nodejs', 'name' => 'NodeJS'],
			['code' => 'react', 'name' => 'React'],
			['code' => 'startup', 'name' => 'Startups'],
		];

		$this->template->allTags = ["php", "nodejs", "react", "startup"];

		$this->template->events = [
			['id' => '001', 'name' => "aaa - 5", 'tags' => ["php"],
				'rates' => ['event' => 5, 'php' => 10], 'description' => "php1 - 10", 'img' => "img/octopus.png"],
			['id' => '002', 'name' => "bbb - 10", 'tags' => ["nodejs", "react"],
				'rates' => ['event' => 10, 'nodejs' => 6, 'react' => 3], 'description' => "node js 1 - 6, react 1 - 3", 'img' => "img/octopus.png"],
			['id' => '003', 'name' => "ccc - 4", 'tags' => ["startup"],
				'rates' => ['event' => 4, 'startup' => 6], 'description' => "startup 1 - 6", 'img' => "img/octopus.png"],
			['id' => '004', 'name' => "ddd - 5", 'tags' => ["nodejs", "startup"],
				'rates' => ['event' => 5, 'nodejs' => 2, 'startup' => 4], 'description' => "node js 2 - 2, startup 2 - 4", 'img' => "img/octopus.png"],
			['id' => '005', 'name' => "eee - 7", 'tags' => ["php", "react"],
				'rates' => ['event' => 7, 'php' => 2, 'react' => 8], 'description' => "php2 - 2, react 2 - 8", 'img' => "img/octopus.png"],

			['id' => '006', 'name' => "fff - 9", 'tags' => ["react"],
				'rates' => ['event' => 9, 'react' => 10], 'description' => "react 3 - 10", 'img' => "img/octopus.png"],

			['id' => '007', 'name' => "ggg - 1", 'tags' => ["nodejs"],
				'rates' => ['event' => 1, 'nodejs' => 10], 'description' => "node js 3 - 10", 'img' => "img/octopus.png"],

			['id' => '008', 'name' => "hhh - 2", 'tags' => ["php"],
				'rates' => ['event' => 2, 'php' => 8], 'description' => "php 3 - 8", 'img' => "img/octopus.png"],

			['id' => '009', 'name' => "iii - 7", 'tags' => ["startup", "react", "nodejs"],
				'rates' => ['event' => 7, 'startup' => 7, 'react' => 3, 'nodejs' => 3], 'description' => "startup 3 - 7, react 4 - 3, nodejs 4 - 3", 'img' => "img/octopus.png"],
		];
	}

}
