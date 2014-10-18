-- MySQL dump 10.13  Distrib 5.5.29, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: epik_users and epik_games
-- ------------------------------------------------------
-- Server version	5.5.29-0ubuntu0.12.04.1

--
-- Dumping data for table `epik_users.games`
--
INSERT INTO `games` (`id`, `name`, `description`, `icon`, `visibility_id`, `genre_id`, `resource_key`, `secret`, `created`, `modified`, `user_id`) VALUES 
	(1, 'General Knowledge IQuiz', '', 0, 2, 1, 'general_knowledge_iquiz', '1af3d54c8d779f96fbb9808f794571500e7b6b02', NOW(), NOW(), 1),
	(2, 'Abstract Data Types IQuiz', '', 0, 2, 1, 'abstract_data_types_iquiz', '2af3d54c8d779f96fbb9808f794571500e7b6b02', NOW(), NOW(), 1),
	(3, 'Abstract Data Types CQuiz', '', 0, 2, 2, 'abstract_data_types_cquiz', '3af3d54c8d779f96fbb9808f794571500e7b6b02', NOW(), NOW(), 1),
	(4, 'Math CQuiz', '', 0, 2, 2, 'math_cquiz', '4af3d54c8d779f96fbb9808f794571500e7b6b02', NOW(), NOW(), 1)