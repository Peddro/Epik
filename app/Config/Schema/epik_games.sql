CREATE SCHEMA IF NOT EXISTS `epik_games` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;

/*---------------------------------------STATIC TABLES----------------------------------------*/

/* Questions Types Table */
CREATE  TABLE IF NOT EXISTS `epik_games`.`questions_types` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(50) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;

/* Resources Types Table */
CREATE  TABLE IF NOT EXISTS `epik_games`.`resources_types` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(50) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;

/* Bonus Types Table */
CREATE  TABLE IF NOT EXISTS `epik_games`.`bonus_types` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(50) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;

/* Scores Types Table */
CREATE  TABLE IF NOT EXISTS `epik_games`.`scores_types` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(50) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


/*-------------------------------------------TABLES-------------------------------------------*/


/* Games Table */
CREATE  TABLE IF NOT EXISTS `epik_games`.`games` (
  `id` BIGINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `game_id_idx` (`id` ASC) ,
  CONSTRAINT `game_id`
    FOREIGN KEY (`id` )
    REFERENCES `epik_users`.`games` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


/* Resources Table */
CREATE  TABLE IF NOT EXISTS `epik_games`.`resources` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `url` VARCHAR(2083) NOT NULL ,
  `external` TINYINT(1) NOT NULL ,
  `original_id` BIGINT UNSIGNED NOT NULL ,
  `type_id` BIGINT UNSIGNED NOT NULL ,
  `game_id` BIGINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `resource_type_idx` (`type_id` ASC) ,
  INDEX `resource_game_idx` (`game_id` ASC) ,
  INDEX `resource_original_idx` (`original_id` ASC) ,
  CONSTRAINT `resource_type`
    FOREIGN KEY (`type_id` )
    REFERENCES `epik_games`.`resources_types` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `resource_game`
    FOREIGN KEY (`game_id` )
    REFERENCES `epik_games`.`games` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


/* Activities Table */
CREATE  TABLE IF NOT EXISTS `epik_games`.`activities` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(50) NOT NULL ,
  `has_hints` TINYINT(1) NOT NULL ,
  `has_resources` TINYINT(1) NOT NULL ,
  `original_id` BIGINT UNSIGNED NOT NULL ,
  `game_id` BIGINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `game_activity_idx` (`game_id` ASC) ,
  INDEX `activity_original_idx` (`original_id` ASC) ,
  CONSTRAINT `activity_game`
    FOREIGN KEY (`game_id` )
    REFERENCES `epik_games`.`games` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


/* Activities Hints Table */
CREATE  TABLE IF NOT EXISTS `epik_games`.`activities_hints` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `content` VARCHAR(200) NOT NULL ,
  `original_id` BIGINT UNSIGNED NOT NULL ,
  `activity_id` BIGINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `game_activity_hint_idx` (`activity_id` ASC) ,
  CONSTRAINT `hint_activity`
    FOREIGN KEY (`activity_id` )
    REFERENCES `epik_games`.`activities` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


/* Activities Resources Table */
CREATE  TABLE IF NOT EXISTS `epik_games`.`activities_resources` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `activity_id` BIGINT UNSIGNED NOT NULL ,
  `resource_id` BIGINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `resource_activity_idx` (`activity_id` ASC) ,
  INDEX `activity_resource_idx` (`resource_id` ASC) ,
  UNIQUE INDEX `no_repeat_game_ar` (`activity_id` ASC, `resource_id` ASC) ,
  CONSTRAINT `resource_activity`
    FOREIGN KEY (`activity_id` )
    REFERENCES `epik_games`.`activities` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `activity_resource`
    FOREIGN KEY (`resource_id` )
    REFERENCES `epik_games`.`resources` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


/* Questions Table */
CREATE  TABLE IF NOT EXISTS `epik_games`.`questions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `content` VARCHAR(200) NOT NULL ,
  `created` DATETIME NOT NULL ,
  `type_id` BIGINT UNSIGNED NOT NULL ,
  `activity_id` BIGINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `game_activity_id_UNIQUE` (`activity_id` ASC) ,
  INDEX `game_question_activity_idx` (`activity_id` ASC) ,
  INDEX `question_type_idx` (`type_id` ASC) ,
  CONSTRAINT `question_activity`
    FOREIGN KEY (`activity_id` )
    REFERENCES `epik_games`.`activities` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `question_type`
    FOREIGN KEY (`type_id` )
    REFERENCES `epik_games`.`questions_types` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


/* Answers Table */
CREATE  TABLE IF NOT EXISTS `epik_games`.`answers` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `content` VARCHAR(200) NOT NULL ,
  `is_correct` TINYINT(1) NOT NULL DEFAULT 0 ,
  `question_id` BIGINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `game_question_answer_idx` (`question_id` ASC) ,
  CONSTRAINT `question_answer`
    FOREIGN KEY (`question_id` )
    REFERENCES `epik_games`.`questions` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


/* LMS Table */
CREATE  TABLE IF NOT EXISTS `epik_games`.`lms` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `url` VARCHAR(200) NOT NULL ,
  `outcome` VARCHAR(200) NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `unique_url` (`url` ASC) )
ENGINE = InnoDB;


/* Sessions Table */
CREATE  TABLE IF NOT EXISTS `epik_games`.`sessions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `lms_id` BIGINT UNSIGNED NULL ,
  `context_id` BIGINT UNSIGNED NULL ,
  `context_name` VARCHAR(200) NULL ,
  `score` FLOAT UNSIGNED NULL ,
  `created` DATETIME NOT NULL ,
  `game_id` BIGINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `session_game_idx` (`game_id` ASC) ,
  INDEX `session_lms_idx` (`lms_id` ASC) ,
  CONSTRAINT `session_game`
    FOREIGN KEY (`game_id` )
    REFERENCES `epik_games`.`games` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `session_lms`
    FOREIGN KEY (`lms_id` )
    REFERENCES `epik_games`.`lms` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


/* Players Table */
CREATE  TABLE IF NOT EXISTS `epik_games`.`players` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(200) NOT NULL ,
  `helps_used` INT UNSIGNED NULL ,
  `helps_given` INT UNSIGNED NULL ,
  `instance_id` VARCHAR(300) NULL ,
  `user_id` BIGINT UNSIGNED NULL ,
  `session_id` BIGINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `player_session_idx` (`session_id` ASC) ,
  CONSTRAINT `player_session`
    FOREIGN KEY (`session_id` )
    REFERENCES `epik_games`.`sessions` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


/* Players Bonus Table */
CREATE  TABLE IF NOT EXISTS `epik_games`.`players_bonus` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `scenario` VARCHAR(50) NOT NULL ,
  `value` FLOAT UNSIGNED NOT NULL ,
  `type_id` BIGINT UNSIGNED NOT NULL ,
  `player_id` BIGINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `bonus_player_idx` (`player_id` ASC) ,
  INDEX `bonus_type_idx` (`type_id` ASC) ,
  CONSTRAINT `bonus_player`
    FOREIGN KEY (`player_id` )
    REFERENCES `epik_games`.`players` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `bonus_type`
    FOREIGN KEY (`type_id` )
    REFERENCES `epik_games`.`bonus_types` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


/* Players Scores Table */
CREATE  TABLE IF NOT EXISTS `epik_games`.`players_scores` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `value` FLOAT UNSIGNED NOT NULL ,
  `type_id` BIGINT UNSIGNED NOT NULL ,
  `player_id` BIGINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `player_score_idx` (`type_id` ASC) ,
  INDEX `player_idx` (`player_id` ASC) ,
  CONSTRAINT `player_score`
    FOREIGN KEY (`type_id` )
    REFERENCES `epik_games`.`scores_types` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `score_player`
    FOREIGN KEY (`player_id` )
    REFERENCES `epik_games`.`players` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


/* Activities Logs Table */
CREATE  TABLE IF NOT EXISTS `epik_games`.`activities_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `reward` FLOAT UNSIGNED NULL ,
  `penalty` FLOAT UNSIGNED NULL ,
  `attempts` INT UNSIGNED NOT NULL ,
  `player_id` BIGINT UNSIGNED NOT NULL ,
  `activity_id` BIGINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `game_activity_log_idx` (`activity_id` ASC) ,
  INDEX `log_player_idx` (`player_id` ASC) ,
  CONSTRAINT `log_activity`
    FOREIGN KEY (`activity_id` )
    REFERENCES `epik_games`.`activities` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `log_player`
    FOREIGN KEY (`player_id` )
    REFERENCES `epik_games`.`players` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


/*-------------------------------------------INSERTS-------------------------------------------*/

/* Questions Types */
INSERT INTO `epik_games`.`questions_types` (`id`, `name`) VALUES (1, 'shortanswer');
INSERT INTO `epik_games`.`questions_types` (`id`, `name`) VALUES (2, 'multichoice');
INSERT INTO `epik_games`.`questions_types` (`id`, `name`) VALUES (3, 'truefalse');

/* Resources Types */
INSERT INTO `epik_games`.`resources_types` (`id`, `name`) VALUES (1, 'audio');
INSERT INTO `epik_games`.`resources_types` (`id`, `name`) VALUES (2, 'image');
INSERT INTO `epik_games`.`resources_types` (`id`, `name`) VALUES (3, 'video');
INSERT INTO `epik_games`.`resources_types` (`id`, `name`) VALUES (4, 'pdf');

/* Bonus Types */
INSERT INTO `epik_games`.`bonus_types` (`id`, `name`) VALUES (1, 'firstToFinish');
INSERT INTO `epik_games`.`bonus_types` (`id`, `name`) VALUES (2, 'collaboration');

/* Scores Types */
INSERT INTO `epik_games`.`scores_types` (`id`, `name`) VALUES (1, 'reward');
INSERT INTO `epik_games`.`scores_types` (`id`, `name`) VALUES (2, 'penalty');
INSERT INTO `epik_games`.`scores_types` (`id`, `name`) VALUES (3, 'collaboration');
INSERT INTO `epik_games`.`scores_types` (`id`, `name`) VALUES (4, 'total');


/*-------------------------------------------USERS-------------------------------------------*/
GRANT ALL PRIVILEGES ON `epik_games`.* TO 'EpikAdmin'@'localhost' IDENTIFIED BY 'normandy' WITH GRANT OPTION;
