<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use App\DatabaseCreator;

final class HomepagePresenter extends Nette\Application\UI\Presenter {

	/** @var App\DatabaseCreator */
	private $dbcreator;

	public function __construct(\App\DatabaseCreator $dbcreator) {
		$this->dbcreator = $dbcreator;
	}
	
	public function startup() {
		parent::startup();
		if ($this->dbcreator->createdb()) {
			$this->flashMessage('database was created.');
		}
	}

}