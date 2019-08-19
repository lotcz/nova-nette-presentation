<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;

final class MeetingPresenter extends Nette\Application\UI\Presenter {
	
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

	public function renderDefault($id): void {
		$meeting = $this->database->table('meeting')->get($id);
		if (empty($meeting)) {
			$this->flashMessage(sprintf('Meeting with id %s not found!', $id));
			$this->redirect('Homepage:default');
		} else {
			$user_id = $this->getUser()->getIdentity()->getId();
			$participant = $this->database->table('meeting_user')->where('user_id = ? AND meeting_id = ?', $user_id, $meeting->id);
			if (count($participant) == 0) {
				$this->database->table('meeting_user')->insert([
					'user_id' => $user_id,
					'meeting_id' => $meeting->id
				]);
			}
			$participants = $meeting->related('meeting_user');
			$user_stories = $meeting->related('user_story');
			$this->template->meeting = $meeting;
			$this->template->participants = $participants;
			$this->template->user_stories = $user_stories;
			$this->template->active_us = $meeting->ref('user_story', 'active_user_story_id');
		}
	}

	public function renderActivateUserStory($user_story_id, $meeting_id): void {
		$this->database->table('meeting')
			->where('id', $meeting_id)
			->update(['active_user_story_id' => $user_story_id]);
		$this->redirect('Meeting:default', $meeting_id);
	}
}
