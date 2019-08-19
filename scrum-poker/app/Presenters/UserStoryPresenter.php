<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;

final class UserStoryPresenter extends Nette\Application\UI\Presenter {
	
	private $meeting_id = null;
	
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
	
	protected function createComponentUserStoryForm() {
		$form = new Nette\Application\UI\Form;
		$form->addText('name', 'Název:');
		$form->addSubmit('submit', 'Uložit');
		$form->addHidden('meeting_id', $this->meeting_id);
		$form->onSuccess[] = [$this, 'userStoryFormSucceeded'];
		return $form;
	}
	
	public function userStoryFormSucceeded(Nette\Application\UI\Form $form, \stdClass $values) {
		$user_story = $this->database->table('user_story')->insert(
			 [
				 'name' => $values->name,
				 'meeting_id' => $values->meeting_id
			 ]
		);
		$this->flashMessage('User Story vytvorena');
		$this->redirect('Meeting:default', $values->meeting_id);
	}

	public function renderAdd($meeting_id): void {
		$this->meeting_id = $meeting_id;
	}
}
