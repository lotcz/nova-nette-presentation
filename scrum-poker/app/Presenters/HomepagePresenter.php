<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use App\DatabaseInitializer;
use Nette\Application\UI\Form;

final class HomepagePresenter extends Nette\Application\UI\Presenter {

	/** @var App\DatabaseInitializer */
	private $dbInitializer;

	/** @var Nette\Database\Context */
	private $database;
	
	/** @var Nette\Security\User */
	private $user;

	public function __construct(DatabaseInitializer $dbInitializer, Nette\Database\Context $database, Nette\Security\User $user) {
		$this->dbInitializer = $dbInitializer;
		$this->database = $database;
		$this->user = $user;
	}

	public function startup(): void {
		parent::startup();
		if ($this->dbInitializer->initializeDb()) {
			$this->flashMessage('Database was initialized.');
		}
	}

	protected function createComponentLoginForm() {
		$form = new Nette\Application\UI\Form;
		$form->addText('name', 'Jméno:')
			->setRequired('Please enter your name.')
			->addRule(Form::MIN_LENGTH, 'Your name has to be at least %d long', 3);;
		$form->addSubmit('login', 'Vstoupit');
		$form->onSuccess[] = [$this, 'loginFormSucceeded'];
		return $form;
	}

	public function loginFormSucceeded(Nette\Application\UI\Form $form, \stdClass $values) {
		$username = $values->name;
		try {
			$existing_user = $this->database->table('user')->where('name', $username);
			if (count($existing_user) == 0) {
				$this->database->table('user')->insert(['name' => $username]);
				$this->flashMessage(sprintf('Hello %s, we created a new user account for you.', $username));
			}
			$this->user->login($username);
			$this->flashMessage(sprintf('Welcome %s.', $username));
			$this->redirect('Meetings:default');
		} catch (Nette\Security\AuthenticationException $e) {
			$this->flashMessage('Something went wrong.', 'error');
		}	
	}

	public function renderDefault(): void {
		$this->database->table('meeting')->insert([
			'name' => 'test',
			'meeting_date' => time()
		]);
		$user = $this->getUser();
		if ($user->isLoggedIn()) {
			$this->flashMessage('You are loggind in.');
		} else {
			$this->flashMessage('You are anonymous.');
		}
	}

}
