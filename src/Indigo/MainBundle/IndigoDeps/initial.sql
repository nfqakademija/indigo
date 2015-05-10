SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE `contests`;
TRUNCATE TABLE `games`;
TRUNCATE TABLE `game_times`;
TRUNCATE TABLE `players_teams`;
TRUNCATE TABLE `ratings`;
TRUNCATE TABLE `reset_password`;
TRUNCATE TABLE `roles`;
TRUNCATE TABLE `tables_status`;
TRUNCATE TABLE `users`;
TRUNCATE TABLE `user_role`;
TRUNCATE TABLE `teams`;

INSERT INTO `tables_status` (`id`, `game_id`, `busy`, `table_id`, `last_swipe_ts`, `last_api_record_id`, `last_api_record_ts`, `last_swiped_card_id`, `last_tableshake_ts`, `url`) VALUES (1, NULL, 0, 1, 0, 0, 0, 0, 0, '');

INSERT INTO `users` (`id`, `active`, `locked`, `username`, `email`, `password`, `salt`, `picture`, `registration_date`, `card_id`, `name`) VALUES (1, 1, 0, 'admin', 'admin@nfqakademija.lt', '52ce760cd35d73dcabed3fea8eb6748d', '20ae87d0ac56ae7a79d8200d007433b9', '/bundles/indigoui/images/anonymous.png', '2015-05-10 18:11:28', NULL, NULL);

INSERT INTO `roles` (`id`, `name`, `role`, `active`) VALUES (1, 'admin', 'ROLE_ADMIN', 1);
INSERT INTO `roles` (`id`, `name`, `role`, `active`) VALUES (2, 'user', 'ROLE_USER', 1);

INSERT INTO `user_role` (`user_id`, `role_id`) VALUES (1, 1);

INSERT INTO `contests` (`id`, `user_id`, `contest_title`, `image_path`, `table_name`, `contest_privacy`, `contest_type`, `contest_creation_date`, `contest_starting_date`, `contest_end_date`, `prize_images_paths`, `prize`, `score_limit`) VALUES (1, NULL, 'NFQ Open contest', NULL, '', 0, 0, '2015-01-01 00:00:00', '2015-01-01 00:00:00', '2030-01-01 00:00:00', NULL, 'Gera nuotaika', 10);

SET FOREIGN_KEY_CHECKS = 1;
