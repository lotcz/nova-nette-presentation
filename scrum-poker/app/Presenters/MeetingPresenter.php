<?php

	declare(strict_types=1);

	namespace App\Presenters;

	use Nette;

	/**
	 * Controller for meeting detail page.
	 */
	final class MeetingPresenter extends Nette\Application\UI\Presenter {


		/**
		 * Represents a database connection. Set automatically with DI.
		 * @var Nette\Database\Context
		*/
		private $database;

		public function __construct(Nette\Database\Context $database) {
			$this->database = $database;
		}

		/**
		 * Checks if user is logged in and redirect to homepage if not.
		 */
		public function startup(): void {
			parent::startup();
			if (!$this->getUser()->isLoggedIn()) {
				$this->flashMessage('You must be logged in!');
				$this->redirect('Homepage:default');
			}
		}

		/**
		 * Default meeting detail page.
		 * @param int $id ID of the meeting.
		 */
		public function renderDefault(int $id): void {
			$meeting = $this->database->table('meeting')->get($id);
			if (empty($meeting)) {
				$this->flashMessage(sprintf('Meeting with id %s not found!', $id));
				$this->redirect('Homepage:default');
			} else {
				$user_id = $this->getUser()->getIdentity()->getId();

				// add user as participant, if he is not a participant yet
				$participant = $this->database->table('meeting_user')->where('user_id = ? AND meeting_id = ?', $user_id, $meeting->id);
				if (count($participant) == 0) {
					$this->database->table('meeting_user')->insert([
						'user_id' => $user_id,
						'meeting_id' => $meeting->id
					]);
				}

				// load current user story and related votes
				$votes = null;
				$my_vote = null;
				$user_story = null;
				$voting = $meeting->ref('voting', 'active_voting_id');
				if ($voting) {
					$user_story = $voting->ref('user_story');
					if ($voting->is_finished) {
						$votes = $voting->related('vote');
					} else {
						//fetch user's vote if he has voted in current voting
						$my_vote = $voting->related('vote')->where('meeting_user.user_id', $user_id)->fetch();
					}
				}

				$this->template->meeting = $meeting;
				$this->template->participants = $meeting->related('meeting_user');
				$this->template->user_stories = $meeting->related('user_story');
				$this->template->active_voting = $voting;
				$this->template->active_user_story = $user_story;
				$this->template->my_vote = $my_vote;
				$this->template->votes = $votes;
			}
		}

		/**
		 * Set user story as active story for the meeting and redirect back to meeting detail.
		 * Active means that participants of the meeting are currently voting about this story.
		 * @param int $user_story_id
		 * @param int $meeting_id
		 */
		public function renderActivateUserStory(int $user_story_id, int $meeting_id): void {
			$voting = $this->database->table('voting')
				->insert([
					'user_story_id' => $user_story_id
				]);
			$this->database->table('meeting')
				->where('id', $meeting_id)
				->update(['active_voting_id' => $voting->id]);
			$this->redirect('Meeting:default', $meeting_id);
		}

		/**
		 * Save participant's vote and redirect back to meeting detail.
		 * @param int $voting_id
		 * @param int $story_points
		 */
		public function renderVote(int $voting_id, int $story_points): void {
			$user_id = $this->getUser()->getIdentity()->getId();
			$voting = $this->database->table('voting')->get($voting_id);
			$meeting = $voting->ref('user_story')->ref('meeting');
			$participant = $meeting->related('meeting_user')->where('user_id', $user_id)->fetch();

			// save vote
			$this->database->table('vote')
				->insert([
					'meeting_user_id' => $participant->id,
					'voting_id' => $voting_id,
					'story_points' => $story_points
				]);

			// close voting if all participants had voted
			if ($voting->related('vote', 'voting_id')->count() == $meeting->related('meeting_user')->count()) {

				$final_score = null;
				$values = array();
				$min = $story_points;
				$max = $story_points;
				$indetermined = 0;

				// gather voting results
				foreach ($voting->related('vote') as $id => $vote) {
					if ($vote->story_points === null || $vote->story_points < 0) {
						$indetermined++;
					} else {
						if ($vote->story_points < $min) {
							$min = $vote->story_points;
						}
						if ($vote->story_points > $max) {
							$max = $vote->story_points;
						}
						if (isset($values[$vote->story_points])) {
							$values[$vote->story_points]++;
						} else {
							$values[$vote->story_points] = 0;
						}
					}
				}

				// save user story voting result if voting was successful
				if ($indetermined === 0 && count($values) <= 2) {
					$final_score = $max;
					$this->database->table('user_story')
						->where('id', $voting->user_story_id)
						->update([
							'story_points' => $final_score
						]);
				}

				// save voting results
				$this->database->table('voting')
					->where('id', $voting->id)
					->update([
						'story_points' => $final_score,
						'is_finished' => 1
					]);
			}

			$this->redirect('Meeting:default', $meeting->id);
		}

		/**
		 * Remove specific participant from a meeting and redirect back to meeting detail.
		 * @param int $meeting_user_id
		 * @param int $meeting_id
		 */
		public function renderRemoveParticipant(int $meeting_user_id, int $meeting_id): void {
			$voting = $this->database->table('meeting_user')
				->delete([
					'id' => $meeting_user_id
				]);
			$this->redirect('Meeting:default', $meeting_id);
		}
	}
