<?php 

declare(strict_types=1);

namespace App;

use Nette;


final class DatabaseCreator {

	/** @var Nette\Database\Connection */
	private $database;

	public function __construct(Nette\Database\Context $database) {
		$this->database = $database;
	}

	private $dumpFilePath;

	public function setDumpFilePath($path) {
		$this->dumpFilePath = $path;
	}
	
	private $dbFilePath;

	public function setDbFilePath($path) {
		$this->dbFilePath = $path;
	}

	public function createdb(): bool {
		if (!file_exists($this->dbFilePath)) {
			Nette\Database\Helpers::loadFromFile(
				$this->database->getConnection(),
				$this->dumpFilePath
			);
			return true;
		}
		return false;
	}

}