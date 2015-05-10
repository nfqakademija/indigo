INSERT INTO `roles` (`id`, `name`, `role`, `active`) VALUES (1, 'Admin', 'ROLE_ADMIN', 1);
INSERT INTO `roles` (`id`, `name`, `role`, `active`) VALUES (2, 'User', 'ROLE_USER', 1);

INSERT INTO `contests` (`id`, `user_id`, `contest_title`, `image_path`, `table_name`, `contest_privacy`, `contest_type`, `contest_creation_date`, `contest_starting_date`, `contest_end_date`, `prize_images_paths`, `prize`, `score_limit`) VALUES (1, NULL, 'OPEN Contest', '/bundles/indigoui/images/contest-open.png', '', 0, 0, '2015-01-01 00:00:00', '2015-01-01 00:00:00', '2115-01-01 00:00:00', NULL, 'Puiki nuotaika, fantastiška atmosfera.', 6);
INSERT INTO `contests` (`id`, `user_id`, `contest_title`, `image_path`, `table_name`, `contest_privacy`, `contest_type`, `contest_creation_date`, `contest_starting_date`, `contest_end_date`, `prize_images_paths`, `prize`, `score_limit`) VALUES (2, NULL, 'Super turnyras (N-14)', '/bundles/indigoui/images/content-box.png', '', 0, 0, '2015-01-01 00:00:00', '2015-05-01 00:00:00', '2015-06-01 00:00:00', NULL, 'Kelionė dviems į saulėtąją Turkiją. ', 8);
INSERT INTO `contests` (`id`, `user_id`, `contest_title`, `image_path`, `table_name`, `contest_privacy`, `contest_type`, `contest_creation_date`, `contest_starting_date`, `contest_end_date`, `prize_images_paths`, `prize`, `score_limit`) VALUES (3, NULL, 'Greičio turnyras', '/bundles/indigoui/images/content-box-2.png', '', 0, 0, '2015-06-01 00:00:00', '2015-05-01 00:00:00', '2015-09-01 00:00:00', NULL, 'Formulės bolido F-3 išbandymas Kačerginėje!!', 5);

INSERT INTO `tables_status` (`id`, `game_id`, `busy`, `table_id`, `last_swipe_ts`, `last_api_record_id`, `last_api_record_ts`, `last_swiped_card_id`, `last_tableshake_ts`, `url`) VALUES (1, NULL, 0, 1, 0, 0, 0, 0, 0, '');