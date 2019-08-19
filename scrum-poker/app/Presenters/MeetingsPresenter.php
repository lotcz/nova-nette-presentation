<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;


final class MeetingsPresenter extends Nette\Application\UI\Presenter {
	
	/** @var Nette\Database\Context */
	private $database;

	public function __construct(Nette\Database\Context $database) {
		$this->database = $database;
	}

	public function startup(): void {
		parent::startup();
		if (!$this->getUser()->isLoggedIn()) {
			$this->flashMessage('You must be logged in!');
			$this->redirect('Homepage:default');
		}
	}

	public function renderDefault(): void {
		$meetings = $this->database->table('meeting');
		$this->template->meetings = $meetings;
	}
}
