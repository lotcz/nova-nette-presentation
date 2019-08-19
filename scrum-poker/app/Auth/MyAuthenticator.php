<?php

declare(strict_types=1);

namespace App;

use Nette;
use Nette\Security;

/**
* Dummy authenticator that works with name only.
* It doesn't provide any security, only user recognition.
*/
class MyAuthenticator implements Nette\Security\IAuthenticator {
		
		/** @var Nette\Database\Context */
		private $database;

		public function __construct(Nette\Database\Context $database) {
				$this->database = $database;
		}

		public function authenticate(array $credentials): Nette\Security\IIdentity {
				list($username) = $credentials;
				$row = $this->database->table('user')
						->where('name', $username)->fetch();

				if (!$row) {
						throw new Nette\Security\AuthenticationException('User not found.');
				}

				return new Nette\Security\Identity($row->id, null, ['username' => $row->name]);
		}
}