CREATE TABLE IF NOT EXISTS `ae_ext_bbs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `playtask_id` int(11) unsigned NOT NULL,
  `date` datetime NOT NULL,
  `title` varchar(255) NOT NULL,
  `msg` text NOT NULL,
  `comment` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `playtask_id` (`playtask_id`),
  KEY `date` (`date`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Constraints for table `ae_ext_diary`
--
ALTER TABLE  `ae_ext_bbs` ADD FOREIGN KEY (  `playtask_id` ) REFERENCES  `activationengine`.`ae_game_play_action` (
  `id`
) ON DELETE CASCADE ON UPDATE NO ACTION ;


CREATE TABLE IF NOT EXISTS `ae_ext_bbs_post` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `bbs_id` int(11) unsigned NOT NULL,
  `parent_id` int(11) unsigned NOT NULL,
  `date` datetime NOT NULL,
  `title` varchar(255) NOT NULL,
  `msg` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `bbs_id` (`bbs_id`),
  KEY `parent_id` (`parent_id`),
  KEY `date` (`date`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


INSERT INTO `ae_game_branch_action_type` (`title`, `icon`, `shortname`, `id_user`, `description`, `version`, `channels`, `uiformat`, `active`, `global`, `githubrepo`, `adminfeedback`, `requestupdate`, `uses_table`, `has_statistics`, `has_export`) VALUES
('BBS (Bulletin board)', 'comments.png', 'bbs', 1, '<p></p><p>This adds a discussion module. Discussions are shared by all players who have this action active.&nbsp;</p>\r\n', '0.1', '', 'HTML 5', 1, 1, '', '', 0, 0, 1, 1);


