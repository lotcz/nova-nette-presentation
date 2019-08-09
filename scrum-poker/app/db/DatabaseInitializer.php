<?php

declare(strict_types=1);

namespace App;

use Nette;


final class DatabaseInitializer {

	/** @var Nette\Database\Connection */
	private $database;

	public function __construct(Nette\Database\Context $database) {
		$this->database = $database;
	}

	private $dumpFilePath;

	public function setDumpFilePath($path) {
		$this->dumpFilePath = $path;
	}

	public function isDbInitialized(): bool {
		$rows = $this->database->fetchAll("SELECT name FROM sqlite_master WHERE type = ? AND name = ?", "table", "meetings");
		return (count($rows) > 0);
	}

	public function resetDb(): void {
		Nette\Database\Helpers::loadFromFile(
			$this->database->getConnection(),
			$this->dumpFilePath
		);
	}

	public function initializeDb(): bool {
		if (!$this->isDbInitialized()) {
			$this->reseDb();
			return true;
		}
		return false;
	}

}
