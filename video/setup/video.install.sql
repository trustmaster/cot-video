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
