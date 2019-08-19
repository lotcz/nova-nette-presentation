<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Utils\DateTime;

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
	
	protected function createComponentMeetingForm() {
		$form = new Nette\Application\UI\Form;
		$date = DateTime::from(time());
		$form->addText('meeting_date', 'Datum:')
			->setRequired('Prosím vložte datum bodovací schůze.')
			->setDefaultValue($date->format(DateTime::ISO8601));
		$form->addText('name', 'Název:');
		$form->addSubmit('submit', 'Zahájit hlasování');
		$form->onSuccess[] = [$this, 'meetingFormSucceeded'];
		return $form;
	}
	
	public function meetingFormSucceeded(Nette\Application\UI\Form $form, \stdClass $values) {
		$date = DateTime::from($values->meeting_date);
		if (!$date) {
			$this->flashMessage('Something went wrong.', 'error');
		} else {
			$name = empty($values->name) ? $date->format(DateTime::ISO8601) : $values->name;
			$meeting = $this->database->table('meeting')->insert(
				[
					'meeting_date' => $date,
					'name' => $name
				]
			);
			$this->database->table('meeting_user')->insert([
				'user_id' => $this->getUser()->getIdentity()->getId(),
				'meeting_id' => $meeting->id,
				'is_admin' => 1
			]);
			$this->flashMessage('Schuze vytvorena');
			$this->redirect('Meeting:default', $meeting->id);
		}
	}
	
	public function renderAdd(): void {
	}
}
