CREATE SCHEMA IF NOT EXISTS `epik_users` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;

/*---------------------------------------STATIC TABLES----------------------------------------*/

/* LMS Table */
CREATE  TABLE IF NOT EXISTS `epik_users`.`lms` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(50) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


/* Games Modes Table */
CREATE  TABLE IF NOT EXISTS `epik_users`.`games_modes` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(50) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


/* Games Genres Table */
CREATE  TABLE IF NOT EXISTS `epik_users`.`games_genres` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(50) NOT NULL ,
  `code` VARCHAR(50) NOT NULL ,
  `instructions` TEXT NOT NULL ,
  `gameover` TEXT NOT NULL ,
  `mode_id` BIGINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `mode_id_idx` (`mode_id` ASC) ,
  CONSTRAINT `mode_id`
    FOREIGN KEY (`mode_id` )
    REFERENCES `epik_users`.`games_modes` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


/* Learning Subjects Table */
CREATE  TABLE IF NOT EXISTS `epik_users`.`learning_subjects` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(50) NOT NULL ,
  `subject_id` BIGINT UNSIGNED NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `subject_id_idx` (`subject_id` ASC) ,
  CONSTRAINT `parent_subject`
    FOREIGN KEY (`subject_id` )
    REFERENCES `epik_users`.`learning_subjects` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


/* Activities Types Table */
CREATE  TABLE IF NOT EXISTS `epik_users`.`activities_types` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(50) NOT NULL ,
  `controller` VARCHAR(50) NOT NULL ,
  `icon` VARCHAR(50) NOT NULL ,
  `allows_resources` TINYINT(1) NOT NULL DEFAULT 0 ,
  `allows_hints` TINYINT(1) NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


/* Genres Activities Table */
CREATE  TABLE IF NOT EXISTS `epik_users`.`genres_activities` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `genre_id` BIGINT UNSIGNED NOT NULL ,
  `type_id` BIGINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `activity_genre_idx` (`genre_id` ASC) ,
  INDEX `genre_activity_idx` (`type_id` ASC) ,
  UNIQUE INDEX `unique_genre_activity` (`genre_id` ASC, `type_id` ASC) ,
  CONSTRAINT `activity_genre`
    FOREIGN KEY (`genre_id` )
    REFERENCES `epik_users`.`games_genres` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `genre_activity`
    FOREIGN KEY (`type_id` )
    REFERENCES `epik_users`.`activities_types` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


/* Questions Types Table */
CREATE  TABLE IF NOT EXISTS `epik_users`.`questions_types` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(50) NOT NULL ,
  `icon` VARCHAR(50) NOT NULL ,
  `max_answers` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


/* Genres Questions Table */
CREATE  TABLE IF NOT EXISTS `epik_users`.`genres_questions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `genre_id` BIGINT UNSIGNED NOT NULL ,
  `type_id` BIGINT UNSIGNED NOT NULL ,
  `resource` TINYINT(1) NOT NULL ,
  `hints` TINYINT(1) NOT NULL ,
  `remove` TINYINT(1) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `genre_question_idx` (`type_id` ASC) ,
  INDEX `question_genre_idx` (`genre_id` ASC) ,
  UNIQUE INDEX `unique_genre_question` (`genre_id` ASC, `type_id` ASC) ,
  CONSTRAINT `genre_question`
    FOREIGN KEY (`type_id` )
    REFERENCES `epik_users`.`questions_types` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `question_genre`
    FOREIGN KEY (`genre_id` )
    REFERENCES `epik_users`.`games_genres` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


/* Resources Types Table */
CREATE  TABLE IF NOT EXISTS `epik_users`.`resources_types` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(50) NOT NULL ,
  `mime` VARCHAR(50) NOT NULL ,
  `icon` VARCHAR(50) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


/* Genres Resources Table */
CREATE  TABLE IF NOT EXISTS `epik_users`.`genres_resources` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `genre_id` BIGINT UNSIGNED NOT NULL ,
  `type_id` BIGINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `resource_genre_idx` (`genre_id` ASC) ,
  INDEX `genre_resource_idx` (`type_id` ASC) ,
  UNIQUE INDEX `unique_genre_resource` (`genre_id` ASC, `type_id` ASC) ,
  CONSTRAINT `resource_genre`
    FOREIGN KEY (`genre_id` )
    REFERENCES `epik_users`.`games_genres` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `genre_resource`
    FOREIGN KEY (`type_id` )
    REFERENCES `epik_users`.`resources_types` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


/* Visibility Types Table */
CREATE  TABLE IF NOT EXISTS `epik_users`.`visibility_types` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


/*-------------------------------------------TABLES-------------------------------------------*/

/* Users Table */
CREATE  TABLE IF NOT EXISTS `epik_users`.`users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `firstname` VARCHAR(100) NOT NULL ,
  `lastname` VARCHAR(100) NOT NULL ,
  `picture` TINYINT(1) NOT NULL DEFAULT 0 ,
  `lms_id` BIGINT UNSIGNED NULL ,
  `lms_url` VARCHAR(2083) NULL ,
  `username` VARCHAR(100) NOT NULL ,
  `email` VARCHAR(100) NOT NULL ,
  `password` VARCHAR(40) NOT NULL ,
  `secret` VARCHAR(40) NOT NULL ,
  `created` DATETIME NOT NULL ,
  `role` VARCHAR(10) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `username_UNIQUE` (`username` ASC) ,
  UNIQUE INDEX `email_UNIQUE` (`email` ASC) ,
  INDEX `lms_id_idx` (`lms_id` ASC) ,
  CONSTRAINT `lms_id`
    FOREIGN KEY (`lms_id` )
    REFERENCES `epik_users`.`lms` (`id` )
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB;


/* Activities Table */
CREATE  TABLE IF NOT EXISTS `epik_users`.`activities` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(50) NOT NULL ,
  `description` TEXT NULL ,
  `lms_id` BIGINT UNSIGNED NULL ,
  `lms_url` VARCHAR(2083) NULL ,
  `external_id` BIGINT UNSIGNED NULL ,
  `type_id` BIGINT UNSIGNED NOT NULL ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  `user_id` BIGINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `activity_per_user` (`name` ASC, `user_id` ASC) ,
  INDEX `activity_type_idx` (`type_id` ASC) ,
  INDEX `user_id_idx` (`user_id` ASC) ,
  INDEX `activity_lms_idx` (`lms_id` ASC) ,
  CONSTRAINT `activity_type`
    FOREIGN KEY (`type_id` )
    REFERENCES `epik_users`.`activities_types` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `activity_user`
    FOREIGN KEY (`user_id` )
    REFERENCES `epik_users`.`users` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `activity_lms`
    FOREIGN KEY (`lms_id` )
    REFERENCES `epik_users`.`lms` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


/* Activities Subjects Table */
CREATE  TABLE IF NOT EXISTS `epik_users`.`activities_subjects` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `activity_id` BIGINT UNSIGNED NOT NULL ,
  `subject_id` BIGINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `no_repeat_subject` (`activity_id` ASC, `subject_id` ASC) ,
  INDEX `activity_id_idx` (`activity_id` ASC) ,
  INDEX `subject_id_idx` (`subject_id` ASC) ,
  CONSTRAINT `subject_activity`
    FOREIGN KEY (`activity_id` )
    REFERENCES `epik_users`.`activities` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `activity_subject`
    FOREIGN KEY (`subject_id` )
    REFERENCES `epik_users`.`learning_subjects` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


/* Activities Hints Table */
CREATE  TABLE IF NOT EXISTS `epik_users`.`activities_hints` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `content` VARCHAR(200) NOT NULL ,
  `activity_id` BIGINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `hint_activity_idx` (`activity_id` ASC) ,
  CONSTRAINT `hint_activity`
    FOREIGN KEY (`activity_id` )
    REFERENCES `epik_users`.`activities` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


/* Questions Table */
CREATE  TABLE IF NOT EXISTS `epik_users`.`questions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `content` VARCHAR(200) NOT NULL ,
  `type_id` BIGINT UNSIGNED NOT NULL ,
  `activity_id` BIGINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `type_idx` (`type_id` ASC) ,
  INDEX `question_activity_idx` (`activity_id` ASC) ,
  UNIQUE INDEX `activity_id_UNIQUE` (`activity_id` ASC) ,
  CONSTRAINT `question_type`
    FOREIGN KEY (`type_id` )
    REFERENCES `epik_users`.`questions_types` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `question_activity`
    FOREIGN KEY (`activity_id` )
    REFERENCES `epik_users`.`activities` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


/* Answers Table */
CREATE  TABLE IF NOT EXISTS `epik_users`.`answers` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `content` VARCHAR(200) NOT NULL ,
  `is_correct` TINYINT(1) NOT NULL DEFAULT 0 ,
  `question_id` BIGINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `question_answer_idx` (`question_id` ASC) ,
  CONSTRAINT `question_answer`
    FOREIGN KEY (`question_id` )
    REFERENCES `epik_users`.`questions` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


/* Questions Groups Table */
CREATE  TABLE IF NOT EXISTS `epik_users`.`questions_groups` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `activity_id` BIGINT UNSIGNED NOT NULL ,
  `question_id` BIGINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `pair` (`activity_id` ASC, `question_id` ASC) ,
  INDEX `group_activity_idx` (`activity_id` ASC) ,
  INDEX `group_question_idx` (`question_id` ASC) ,
  CONSTRAINT `group_activity`
    FOREIGN KEY (`activity_id` )
    REFERENCES `epik_users`.`activities` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `group_question`
    FOREIGN KEY (`question_id` )
    REFERENCES `epik_users`.`questions` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


/* Resources Table */
CREATE  TABLE IF NOT EXISTS `epik_users`.`resources` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(50) NOT NULL ,
  `description` TEXT NULL,
  `source` VARCHAR(2083) NULL ,
  `lms_id` BIGINT UNSIGNED NULL ,
  `lms_url` VARCHAR(2083) NULL ,
  `external_id` BIGINT UNSIGNED NULL ,
  `type_id` BIGINT UNSIGNED NOT NULL ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  `user_id` BIGINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `resource_per_user` (`name` ASC, `user_id` ASC) ,
  INDEX `resource_type_idx` (`type_id` ASC) ,
  INDEX `resource_user_idx` (`user_id` ASC) ,
  INDEX `resource_lms_idx` (`lms_id` ASC) ,
  CONSTRAINT `resource_type`
    FOREIGN KEY (`type_id` )
    REFERENCES `epik_users`.`resources_types` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `resource_user`
    FOREIGN KEY (`user_id` )
    REFERENCES `epik_users`.`users` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `resource_lms`
    FOREIGN KEY (`lms_id` )
    REFERENCES `epik_users`.`lms` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


/* Resources Subjects Table */
CREATE  TABLE IF NOT EXISTS `epik_users`.`resources_subjects` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `resource_id` BIGINT UNSIGNED NOT NULL ,
  `subject_id` BIGINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `no_repeat_subject` (`resource_id` ASC, `subject_id` ASC) ,
  INDEX `resource_id_idx` (`resource_id` ASC) ,
  INDEX `subject_id_idx` (`subject_id` ASC) ,
  CONSTRAINT `subject_resource`
    FOREIGN KEY (`resource_id` )
    REFERENCES `epik_users`.`resources` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `resource_subject`
    FOREIGN KEY (`subject_id` )
    REFERENCES `epik_users`.`learning_subjects` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


/* Activities Resources Table */
CREATE  TABLE IF NOT EXISTS `epik_users`.`activities_resources` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `activity_id` BIGINT UNSIGNED NOT NULL ,
  `resource_id` BIGINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `no_repeat_resource1` (`activity_id` ASC, `resource_id` ASC) ,
  INDEX `activity_id_idx1` (`activity_id` ASC) ,
  INDEX `resource_id_idx1` (`resource_id` ASC) ,
  CONSTRAINT `resource_activity`
    FOREIGN KEY (`activity_id` )
    REFERENCES `epik_users`.`activities` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `activity_resource`
    FOREIGN KEY (`resource_id` )
    REFERENCES `epik_users`.`resources` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


/* Projects Table */
CREATE  TABLE IF NOT EXISTS `epik_users`.`projects` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(50) NOT NULL ,
  `description` TEXT NULL ,
  `genre_id` BIGINT UNSIGNED NOT NULL ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  `user_id` BIGINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `user_id_idx` (`user_id` ASC) ,
  UNIQUE INDEX `name_per_user` USING BTREE (`name` ASC, `user_id` ASC) ,
  INDEX `project_genre_idx` (`genre_id` ASC) ,
  CONSTRAINT `project_user`
    FOREIGN KEY (`user_id` )
    REFERENCES `epik_users`.`users` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `project_genre`
    FOREIGN KEY (`genre_id` )
    REFERENCES `epik_users`.`games_genres` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


/* Projects Activities Table */
CREATE  TABLE IF NOT EXISTS `epik_users`.`projects_activities` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `project_id` BIGINT UNSIGNED NOT NULL ,
  `activity_id` BIGINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `no_repeat_activity` (`project_id` ASC, `activity_id` ASC) ,
  INDEX `project_id_idx` (`project_id` ASC) ,
  INDEX `activity_id_idx` (`activity_id` ASC) ,
  CONSTRAINT `activity_project`
    FOREIGN KEY (`project_id` )
    REFERENCES `epik_users`.`projects` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `project_activity`
    FOREIGN KEY (`activity_id` )
    REFERENCES `epik_users`.`activities` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


/* Projects Resources Table */
CREATE  TABLE IF NOT EXISTS `epik_users`.`projects_resources` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `project_id` BIGINT UNSIGNED NOT NULL ,
  `resource_id` BIGINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `no_repeat_resource` (`project_id` ASC, `resource_id` ASC) ,
  INDEX `project_id_idx` (`project_id` ASC) ,
  INDEX `resource_id_idx` (`resource_id` ASC) ,
  CONSTRAINT `resource_project`
    FOREIGN KEY (`project_id` )
    REFERENCES `epik_users`.`projects` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `project_resource`
    FOREIGN KEY (`resource_id` )
    REFERENCES `epik_users`.`resources` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


/* Templates Table */
CREATE  TABLE IF NOT EXISTS `epik_users`.`templates` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(50) NOT NULL ,
  `description` TEXT NULL ,
  `image` TINYINT(1) NOT NULL DEFAULT 0 ,
  `visibility_id` BIGINT UNSIGNED NOT NULL ,
  `genre_id` BIGINT UNSIGNED NOT NULL ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  `user_id` BIGINT UNSIGNED NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `visibility_id_idx` (`visibility_id` ASC) ,
  INDEX `genre_id_idx` (`genre_id` ASC) ,
  INDEX `user_id_idx` (`user_id` ASC) ,
  CONSTRAINT `template_visibility`
    FOREIGN KEY (`visibility_id` )
    REFERENCES `epik_users`.`visibility_types` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `template_genre`
    FOREIGN KEY (`genre_id` )
    REFERENCES `epik_users`.`games_genres` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `template_user`
    FOREIGN KEY (`user_id` )
    REFERENCES `epik_users`.`users` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


/* Scenarios Templates Table */
CREATE  TABLE IF NOT EXISTS `epik_users`.`scenarios_templates` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(50) NOT NULL ,
  `description` TEXT NULL ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


/* Genres Scenarios Table */
CREATE  TABLE IF NOT EXISTS `epik_users`.`genres_scenarios` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `genre_id` BIGINT UNSIGNED NOT NULL ,
  `scenario_id` BIGINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `genre_scenario_idx` (`scenario_id` ASC) ,
  INDEX `scenario_genre_idx` (`genre_id` ASC) ,
  CONSTRAINT `genre_scenario`
    FOREIGN KEY (`scenario_id` )
    REFERENCES `epik_users`.`scenarios_templates` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `scenario_genre`
    FOREIGN KEY (`genre_id` )
    REFERENCES `epik_users`.`games_genres` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


/* Games Table */
CREATE  TABLE IF NOT EXISTS `epik_users`.`games` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(50) NOT NULL ,
  `description` TEXT NULL ,
  `icon` TINYINT(1) NOT NULL DEFAULT 0 ,
  `visibility_id` BIGINT UNSIGNED NOT NULL ,
  `genre_id` BIGINT UNSIGNED NOT NULL ,
  `resource_key` VARCHAR(100) NOT NULL ,
  `secret` VARCHAR(40) NOT NULL ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  `user_id` BIGINT UNSIGNED NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `game_per_user` (`name` ASC, `user_id` ASC) ,
  INDEX `visibility_type_idx` (`visibility_id` ASC) ,
  INDEX `game_user_idx` (`user_id` ASC) ,
  INDEX `game_genre_idx` (`genre_id` ASC) ,
  CONSTRAINT `game_visibility`
    FOREIGN KEY (`visibility_id` )
    REFERENCES `epik_users`.`visibility_types` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `game_user`
    FOREIGN KEY (`user_id` )
    REFERENCES `epik_users`.`users` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `game_genre`
    FOREIGN KEY (`genre_id` )
    REFERENCES `epik_users`.`games_genres` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


/* Games Subjects Table */
CREATE  TABLE IF NOT EXISTS `epik_users`.`games_subjects` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `game_id` BIGINT UNSIGNED NULL ,
  `subject_id` BIGINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `no_repeat_subject` (`game_id` ASC, `subject_id` ASC) ,
  INDEX `game_id_idx` (`game_id` ASC) ,
  INDEX `subject_id_idx` (`subject_id` ASC) ,
  CONSTRAINT `subject_game`
    FOREIGN KEY (`game_id` )
    REFERENCES `epik_users`.`games` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `game_subject`
    FOREIGN KEY (`subject_id` )
    REFERENCES `epik_users`.`learning_subjects` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


/* Logins Table */
CREATE  TABLE IF NOT EXISTS `epik_users`.`logins` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `user_id` BIGINT UNSIGNED NOT NULL ,
  `created` DATETIME NOT NULL ,
  `user_status` VARCHAR(10) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `user_login_idx` (`user_id` ASC) ,
  CONSTRAINT `user_login`
    FOREIGN KEY (`user_id` )
    REFERENCES `epik_users`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB;


/* Logs Table */
CREATE  TABLE IF NOT EXISTS `epik_users`.`logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `table_name` VARCHAR(50) NOT NULL ,
  `row_id` BIGINT UNSIGNED NOT NULL ,
  `row_name` VARCHAR(50) NOT NULL ,
  `operation` VARCHAR(10) NOT NULL ,
  `user_id` BIGINT UNSIGNED NULL ,
  `user_name` VARCHAR(200) NOT NULL ,
  `created` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `log_user_idx` (`user_id` ASC) ,
  CONSTRAINT `log_user`
    FOREIGN KEY (`user_id` )
    REFERENCES `epik_users`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB;


/*-------------------------------------------INSERTS-------------------------------------------*/

/* LMS */
INSERT INTO `epik_users`.`lms` (`id`, `name`) VALUES (1, 'Moodle');
INSERT INTO `epik_users`.`lms` (`id`, `name`) VALUES (2, 'Sakai');
INSERT INTO `epik_users`.`lms` (`id`, `name`) VALUES (3, 'Blackboard');
INSERT INTO `epik_users`.`lms` (`id`, `name`) VALUES (4, 'Desire2Learn');

/* Games Modes */
INSERT INTO `epik_users`.`games_modes` (`id`, `name`) VALUES (1, 'Singleplayer');
INSERT INTO `epik_users`.`games_modes` (`id`, `name`) VALUES (2, 'Multiplayer');

/* Games Genres */
INSERT INTO `epik_users`.`games_genres` (`id`, `name`, `code`, `instructions`, `gameover`, `mode_id`) VALUES (1, 'Individual Quiz', 'IndividualQuiz', '<p>This game must be played by one player.</p>\n\n<p>During the game will be presented to the player:</p>\n<ul>\n	<li><b>Lectures</b>: to introduce a certain topic that may be important for the activities to come;</li>\n	<li><b>Activities</b>: or more precisely, short answer, multiple choice and true or false questions.</li>\n</ul>\n\n<p>Each activity has a score and helps associated to it. The activity score is the points that the player will earn by solving it correctly. If his/hers solution is incorrect, the activity score is reduced on a certain percentage. Helps can be one of the following:</p>\n<ul>\n	<li><b>Learning materials consultation</b>: by requesting this help a audio, image, video, or PDF file related with the question topic is displayed to the player;</li>\n	<li><b>Request hints</b>: by requesting this help a hint is chosen and given to the player;</li>\n	<li><b>Request answers removal</b>: by requesting this help half of the available answers for the question are removed.</li>\n</ul>\n\n<p>To progress in the game one of the following situations must be satisfied:</p>\n<ul>\n	<li><b>Continue</b>: the player clicks on continue;</li>\n	<li><b>Skip</b>: the player clicks on skip;</li>\n	<li><b>All Finished</b>: the player finished solving all activities being currently displayed him/her;</li>\n	<li><b>Timeout</b>: the time to read lectures, consult resources, or solve activities is over.</li>\n</ul>', '<p class=\"center\">You lost the game.</p>\n\n<p class=\"center\">This happens when your score is less than zero, or communication with the server is lost.</p>\n\n<p class=\"center\">Since this happened no information was stored about your progress in the game, but if you want you can try again by clicking the button bellow.</p>\n\n<p class=\"center\">Wish you luck and a epic experience!</p>', 1);
INSERT INTO `epik_users`.`games_genres` (`id`, `name`, `code`, `instructions`, `gameover`, `mode_id`) VALUES (2, 'Collaborative Quiz', 'CollaborativeQuiz', '<p>This game must be played by two or more players.\n\n<p>During the game will be presented to each player:</p>\n<ul>\n	<li><b>Lectures</b>: to introduce a certain topic that may be important for the activities to come;</li>\n	<li><b>Activities</b>: or more precisely, short answer, multiple choice and true or false questions.</li>\n</ul>\n\n<p>Each activity has a score and helps associated to it. The activity score is the points that players will earn by solving it correctly. If their solution is incorrect, the activity score is reduced on a certain percentage. Helps can be one of the following:</p>\n<ul>\n	<li><b>Learning materials consultation</b>: by requesting this help a audio, image, video, or PDF file related with the question topic is displayed to the player;</li>\n	<li><b>Request hints</b>: by requesting this help another player is chosen to send the current player a hint related to the question;</li>\n	<li><b>Request answers removal</b>: by requesting this help another player will be asked to remove half of the available answers for a certain question.</li>\n</ul>\n\n<p>While waiting for another player to answer your help request, you can continue answering questions.</p>\n\n<p>To progress in the game all players must be synchronised, this means that if a player finishes a lecture, or if he finishes solving all activities, he must wait for others to continue. The game continues when one of the following situations is satisfied:</p>\n<ul>\n	<li><b>Continue</b>: all players clicked on continue;</li>\n	<li><b>Skip</b>: one or more players clicked on skip and the others finished solving the activities;</li>\n	<li><b>All Finished</b>: all players finished solving the activities being currently displayed to them;</li>\n	<li><b>Timeout</b>: the time to read lectures, consult resources, or solve activities is over.</li>\n</ul>\n\n<p>While waiting for other players to continue, the player can still receive help requests in order to help his colleagues.</p>', '<p class=\"center\">Your team lost the game.</p>\n\n<p class=\"center\">This happens when your team score is less than zero, or too much players leave the game, or communication with the server is lost.</p>\n\n<p class=\"center\">Since this happened no information was stored about your progress in the game, but if you want you can try again by clicking the button bellow.</p>\n\n<p class=\"center\">Wish you luck and a epic experience!</p>', 2);

/* Learning Subjects */
INSERT INTO `epik_users`.`learning_subjects` (`id`, `name`, `subject_id`) VALUES (1, 'Arts', NULL);
INSERT INTO `epik_users`.`learning_subjects` (`id`, `name`, `subject_id`) VALUES (2, 'Language & Literature', NULL);
INSERT INTO `epik_users`.`learning_subjects` (`id`, `name`, `subject_id`) VALUES (3, 'Humanities', NULL);
INSERT INTO `epik_users`.`learning_subjects` (`id`, `name`, `subject_id`) VALUES (4, 'Mathematics', NULL);
INSERT INTO `epik_users`.`learning_subjects` (`id`, `name`, `subject_id`) VALUES (5, 'Science & Tecnology', NULL);

/* Activities Types */
INSERT INTO `epik_users`.`activities_types` (`id`, `name`, `controller`, `icon`, `allows_resources`, `allows_hints`) VALUES (1, 'Question', 'questions', 'question', 1, 1);
INSERT INTO `epik_users`.`activities_types` (`id`, `name`, `controller`, `icon`, `allows_resources`, `allows_hints`) VALUES (2, 'Questions Group', 'questions_groups', 'group', 0, 0);

/* Genres Activities */
INSERT INTO `epik_users`.`genres_activities` (`id`, `genre_id`, `type_id`) VALUES (NULL, 1, 1);
INSERT INTO `epik_users`.`genres_activities` (`id`, `genre_id`, `type_id`) VALUES (NULL, 1, 2);
INSERT INTO `epik_users`.`genres_activities` (`id`, `genre_id`, `type_id`) VALUES (NULL, 2, 1);
INSERT INTO `epik_users`.`genres_activities` (`id`, `genre_id`, `type_id`) VALUES (NULL, 2, 2);

/* Questions Types */
INSERT INTO `epik_users`.`questions_types` (`id`, `name`, `icon`, `max_answers`) VALUES (1, 'Short Answer', 'shortanswer', 1);
INSERT INTO `epik_users`.`questions_types` (`id`, `name`, `icon`, `max_answers`) VALUES (2, 'Multichoice', 'multichoice', 5);
INSERT INTO `epik_users`.`questions_types` (`id`, `name`, `icon`, `max_answers`) VALUES (3, 'True or False', 'truefalse', 2);

/* Genres Questions */
INSERT INTO `epik_users`.`genres_questions` (`id`, `genre_id`, `type_id`, `resource`, `hints`, `remove`) VALUES (NULL, 1, 1, 1, 1, 0);
INSERT INTO `epik_users`.`genres_questions` (`id`, `genre_id`, `type_id`, `resource`, `hints`, `remove`) VALUES (NULL, 1, 2, 1, 1, 1);
INSERT INTO `epik_users`.`genres_questions` (`id`, `genre_id`, `type_id`, `resource`, `hints`, `remove`) VALUES (NULL, 1, 3, 1, 1, 0);
INSERT INTO `epik_users`.`genres_questions` (`id`, `genre_id`, `type_id`, `resource`, `hints`, `remove`) VALUES (NULL, 2, 1, 1, 1, 0);
INSERT INTO `epik_users`.`genres_questions` (`id`, `genre_id`, `type_id`, `resource`, `hints`, `remove`) VALUES (NULL, 2, 2, 1, 1, 1);
INSERT INTO `epik_users`.`genres_questions` (`id`, `genre_id`, `type_id`, `resource`, `hints`, `remove`) VALUES (NULL, 2, 3, 1, 1, 0);

/* Resources Types */
INSERT INTO `epik_users`.`resources_types` (`id`, `name`, `mime`, `icon`) VALUES (1, 'Audio', 'audio', 'audio');
INSERT INTO `epik_users`.`resources_types` (`id`, `name`, `mime`, `icon`) VALUES (2, 'Image', 'image', 'image');
INSERT INTO `epik_users`.`resources_types` (`id`, `name`, `mime`, `icon`) VALUES (3, 'Video', 'video', 'video');
INSERT INTO `epik_users`.`resources_types` (`id`, `name`, `mime`, `icon`) VALUES (4, 'PDF', 'application', 'pdf');

/* Genres Resources */
INSERT INTO `epik_users`.`genres_resources` (`id`, `genre_id`, `type_id`) VALUES (NULL, 1, 1);
INSERT INTO `epik_users`.`genres_resources` (`id`, `genre_id`, `type_id`) VALUES (NULL, 1, 2);
INSERT INTO `epik_users`.`genres_resources` (`id`, `genre_id`, `type_id`) VALUES (NULL, 1, 3);
INSERT INTO `epik_users`.`genres_resources` (`id`, `genre_id`, `type_id`) VALUES (NULL, 1, 4);
INSERT INTO `epik_users`.`genres_resources` (`id`, `genre_id`, `type_id`) VALUES (NULL, 2, 1);
INSERT INTO `epik_users`.`genres_resources` (`id`, `genre_id`, `type_id`) VALUES (NULL, 2, 2);
INSERT INTO `epik_users`.`genres_resources` (`id`, `genre_id`, `type_id`) VALUES (NULL, 2, 3);
INSERT INTO `epik_users`.`genres_resources` (`id`, `genre_id`, `type_id`) VALUES (NULL, 2, 4);

/* Visibility Types */
INSERT INTO `epik_users`.`visibility_types` (`id`, `name`) VALUES (1, 'base');
INSERT INTO `epik_users`.`visibility_types` (`id`, `name`) VALUES (2, 'private');
INSERT INTO `epik_users`.`visibility_types` (`id`, `name`) VALUES (3, 'public');

/* Users */
INSERT INTO `epik_users`.`users` (`id`, `firstname`, `lastname`, `picture`, `lms_id`, `lms_url`, `username`, `email`, `password`, `secret`, `created`, `role`) VALUES (1, 'Epik', 'Admin', 0, NULL, NULL, 'admin', 'admin@nomail.com', 'cbc46734605f59fdeac4f6f13cfddf1e58bbc4d0', 'cbc46734605f59fdeac4f6f13cfddf1e58bbc4d0', 'CURRENT_TIMESTAMP', 'admin');

/* Templates */
INSERT INTO `epik_users`.`templates` (`id`, `name`, `description`, `image`, `visibility_id`, `genre_id`, `created`, `modified`, `user_id`) VALUES (1, 'Blank', 'Start with a empty project for a singleplayer game.', 0, 1, 1, NOW(), NOW(), NULL);
INSERT INTO `epik_users`.`templates` (`id`, `name`, `description`, `image`, `visibility_id`, `genre_id`, `created`, `modified`, `user_id`) VALUES (2, 'Blank', 'Start with a empty project for a collaborative quiz game.', 0, 1, 2, NOW(), NOW(), NULL);
INSERT INTO `epik_users`.`templates` (`id`, `name`, `description`, `image`, `visibility_id`, `genre_id`, `created`, `modified`, `user_id`) VALUES (3, '4 Lectures + 12 Questions + 4 Hard Questions', 'This template is composed by 3 lecture scenarios and 5 activities scenarios. All of them contain some text, images, videos, and PDF files. You can always add or remove some of the contents if you don\'t need them.<br/><br/>One of the lecture scenarios is used to present two different and concise lectures. After each lecture scenario, there is a activities scenario with 4 questions, which must be of easy or medium difficulty.<br/><br/>The last activities scenario must be used to ask hard questions about all the topics presented.<br/><br/>There is also a explanatory scenario to be used after a skip.', 1, 1, 2, NOW(), NOW(), NULL);

/* Scenarios Templates */
INSERT INTO `epik_users`.`scenarios_templates` (`id`, `name`, `description`, `created`, `modified`) VALUES (1, '1 Lecture + 1 Image', 'Scenario composed by a heading, a paragraph and an image. Ideal to present a concept to players, which will be important in the following scenarios.', NOW(), NOW());
INSERT INTO `epik_users`.`scenarios_templates` (`id`, `name`, `description`, `created`, `modified`) VALUES (2, '1 Lecture + 2 Questions', 'Scenario composed by a heading, a paragraph, an image, and two questions. Ideal to present a concept to players and at the same time ask questions about it.', NOW(), NOW());
INSERT INTO `epik_users`.`scenarios_templates` (`id`, `name`, `description`, `created`, `modified`) VALUES (3, '4 Questions + Image', 'Scenarios composed by four questions around an image. Ideal to ask several questions to players about one or more concepts, and to provide several collaboration situations.', NOW(), NOW());
INSERT INTO `epik_users`.`scenarios_templates` (`id`, `name`, `description`, `created`, `modified`) VALUES (4, '2 Lectures + 2 Questions', 'Scenario composed by a heading, a circle, an image, and two questions. Ideal to make questions about two different concepts, using different ways to present them.', NOW(), NOW());
INSERT INTO `epik_users`.`scenarios_templates` (`id`, `name`, `description`, `created`, `modified`) VALUES (5, '2 Lectures + 2 Images', 'Scenario composed by two headings, two paragraphs and two images. Ideal to present two small concepts to players before making questions about them.', NOW(), NOW());
INSERT INTO `epik_users`.`scenarios_templates` (`id`, `name`, `description`, `created`, `modified`) VALUES (6, '4 Questions + 2 Images', 'Scenario composed by two pairs of questions and two images. Each pair is related to one image. Ideal to ask questions about two different concepts.', NOW(), NOW());

/* Scenarios Genres */
INSERT INTO `epik_users`.`genres_scenarios` (`id`, `genre_id`, `scenario_id`) VALUES (NULL, 1, 1);
INSERT INTO `epik_users`.`genres_scenarios` (`id`, `genre_id`, `scenario_id`) VALUES (NULL, 1, 2);
INSERT INTO `epik_users`.`genres_scenarios` (`id`, `genre_id`, `scenario_id`) VALUES (NULL, 1, 3);
INSERT INTO `epik_users`.`genres_scenarios` (`id`, `genre_id`, `scenario_id`) VALUES (NULL, 1, 4);
INSERT INTO `epik_users`.`genres_scenarios` (`id`, `genre_id`, `scenario_id`) VALUES (NULL, 1, 5);
INSERT INTO `epik_users`.`genres_scenarios` (`id`, `genre_id`, `scenario_id`) VALUES (NULL, 1, 6);
INSERT INTO `epik_users`.`genres_scenarios` (`id`, `genre_id`, `scenario_id`) VALUES (NULL, 2, 1);
INSERT INTO `epik_users`.`genres_scenarios` (`id`, `genre_id`, `scenario_id`) VALUES (NULL, 2, 2);
INSERT INTO `epik_users`.`genres_scenarios` (`id`, `genre_id`, `scenario_id`) VALUES (NULL, 2, 3);
INSERT INTO `epik_users`.`genres_scenarios` (`id`, `genre_id`, `scenario_id`) VALUES (NULL, 2, 4);
INSERT INTO `epik_users`.`genres_scenarios` (`id`, `genre_id`, `scenario_id`) VALUES (NULL, 2, 5);
INSERT INTO `epik_users`.`genres_scenarios` (`id`, `genre_id`, `scenario_id`) VALUES (NULL, 2, 6);



/*-------------------------------------------USERS-------------------------------------------*/
GRANT ALL PRIVILEGES ON `epik_users`.* TO 'EpikAdmin'@'localhost' IDENTIFIED BY 'normandy' WITH GRANT OPTION;
