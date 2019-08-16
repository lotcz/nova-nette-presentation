DROP TABLE IF EXISTS meeting;

CREATE TABLE meeting (
	id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
	name VARCHAR(100) NULL,
	meeting_date DATETIME NOT NULL
);

CREATE INDEX idx_meeting_date ON meeting(meeting_date);

DROP TABLE IF EXISTS user;

CREATE TABLE user (
	id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
	name VARCHAR(100) NULL
);

DROP TABLE IF EXISTS meeting_participant;

CREATE TABLE meeting_participant (
	id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
	user_id INTEGER NOT NULL,
	meeting_id INTEGER NOT NULL,
	is_admin INTEGER(1) NOT NULL DEFAULT(0),
	FOREIGN KEY(user_id) REFERENCES user(id),
	FOREIGN KEY(meeting_id) REFERENCES meeting(id)
);

CREATE UNIQUE INDEX idx_meeting_participant ON meeting_participant(meeting_id, user_id);

DROP TABLE IF EXISTS user_story;

CREATE TABLE user_story (
	id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
	name VARCHAR(100) NULL,
	story_points INTEGER NULL,
	meeting_id INTEGER NOT NULL,
	FOREIGN KEY(meeting_id) REFERENCES meeting(id)
);

CREATE INDEX idx_user_story_meeting ON user_story(meeting_id);

DROP TABLE IF EXISTS voting;

CREATE TABLE voting (
	id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
	story_points INTEGER NULL,
	user_story_id INTEGER NOT NULL,
	FOREIGN KEY(user_story_id) REFERENCES user_story(id)
);

CREATE INDEX idx_voting_user_story ON voting(user_story_id);

DROP TABLE IF EXISTS vote;

CREATE TABLE vote (
	story_points INTEGER NOT NULL,
	voting_id INTEGER NOT NULL,
	meeting_participant_id INTEGER NOT NULL,
	FOREIGN KEY(voting_id) REFERENCES voting(id),
	FOREIGN KEY(meeting_participant_id) REFERENCES meeting_participant(id)
);

CREATE UNIQUE INDEX idx_vote ON vote(voting_id, meeting_participant_id);