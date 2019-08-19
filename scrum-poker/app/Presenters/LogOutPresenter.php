<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;

final class LogOutPresenter extends Nette\Application\UI\Presenter {

	/** @var Nette\Security\User */
	private $user;

	public function __construct(Nette\Security\User $user) {
		$this->user = $user;
	}

	public function renderDefault(): void {
		$this->user->logout();
		$this->flashMessage('Byl jste odhlášen.');
		$this->redirect('Homepage:default');
	}

}
