CREATE TABLE `ae_ext_mobilematching` (
        `id` int(11) UNSIGNED NOT NULL,
        `game_id` int(11) UNSIGNED NOT NULL,
        `user_id` int(11) UNSIGNED NOT NULL,
        `play_id` int(11) UNSIGNED NOT NULL,
        `lat` decimal(11,8) NOT NULL,
        `lon` decimal(11,8) NOT NULL,
        `matches` text NOT NULL,
        `chats` text NOT NULL,
        `unmatch` text NOT NULL,
        `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `gender` varchar(50) NOT NULL,
        `twoway_matches` text NOT NULL,
        `match_always` tinyint(1) NOT NULL DEFAULT '0',
        `notifications` text NOT NULL,
        `score` tinyint(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ae_ext_mobilematching`
--
ALTER TABLE `ae_ext_mobilematching`
ADD PRIMARY KEY (`id`),
ADD KEY `game_id` (`game_id`),
ADD KEY `user_id` (`user_id`),
ADD KEY `play_id` (`play_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ae_ext_mobilematching`
--
ALTER TABLE `ae_ext_mobilematching`
MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `ae_ext_mobilematching`
--
ALTER TABLE `ae_ext_mobilematching`
ADD CONSTRAINT `play` FOREIGN KEY (`play_id`) REFERENCES `ae_game_play` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
ADD CONSTRAINT `game` FOREIGN KEY (`game_id`) REFERENCES `ae_game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
ADD CONSTRAINT `user` FOREIGN KEY (`user_id`) REFERENCES `usergroups_user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
