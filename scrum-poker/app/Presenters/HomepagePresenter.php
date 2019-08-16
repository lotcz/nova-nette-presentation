<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use App\DatabaseInitializer;

final class HomepagePresenter extends Nette\Application\UI\Presenter {

	/** @var DatabaseInitializer */
	private $dbInitializer;

	/** @var Nette\Database\Context */
	private $database;

	public function __construct(DatabaseInitializer $dbInitializer, Nette\Database\Context $database) {
		$this->dbInitializer = $dbInitializer;
		$this->database = $database;
	}

	public function startup(): void {
		parent::startup();
		if ($this->dbInitializer->initializeDb()) {
			$this->flashMessage('Database was initialized.');
		}
	}

	public function renderDefault(): void {
		$this->database->table('meeting')->insert([
			'name' => 'test',
			'meeting_date' => time()
		]);
	}

}
