INSERT INTO `cot_auth` (`auth_groupid`, `auth_code`, `auth_option`,
  `auth_rights`, `auth_rights_lock`, `auth_setbyuserid`) VALUES
(1, 'video', 'system',	5,		250,	1),
(2, 'video', 'system',	1,		254,	1),
(3, 'video', 'system',	0,		255,	1),
(4, 'video', 'system',	7,		0,		1),
(5, 'video', 'system',	255,	255,	1),
(6, 'video', 'system',	135,	0,		1);

CREATE TABLE IF NOT EXISTS `cot_videos` (
	`vid_id` INT NOT NULL AUTO_INCREMENT,
	`vid_cat` VARCHAR(64) NOT NULL,
	`vid_title` VARCHAR(255) NOT NULL,
	`vid_source` VARCHAR(32) NOT NULL DEFAULT 'youtube',
	`vid_code` VARCHAR(64) NOT NULL,
	`vid_added` INT NOT NULL DEFAULT 0,
	`vid_user` INT NOT NULL,
	`vid_order` SMALLINT NOT NULL DEFAULT 50,
	PRIMARY KEY(`vid_id`),
	KEY(`vid_cat`),
	KEY(`vid_added`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `cot_structure` (`structure_area`, `structure_code`, `structure_path`, `structure_tpl`, `structure_title`,
   `structure_desc`, `structure_icon`, `structure_locked`, `structure_count`) VALUES
('video', 'system', '999', '', 'System', '', '', 0, 0);