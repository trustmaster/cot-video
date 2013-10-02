
UPDATE `cot_videos` SET `vid_cat`='system' WHERE `vid_cat`='';

INSERT INTO `cot_auth` (`auth_groupid`, `auth_code`, `auth_option`,
  `auth_rights`, `auth_rights_lock`, `auth_setbyuserid`) VALUES 
(1, 'video', 'system',	5,		250,	1),
(2, 'video', 'system',	1,		254,	1),
(3, 'video', 'system',	0,		255,	1),
(4, 'video', 'system',	7,		0,		1),
(5, 'video', 'system',	255,	255,	1),
(6, 'video', 'system',	135,	0,		1);

INSERT INTO `cot_structure` (`structure_area`, `structure_code`, `structure_path`, `structure_tpl`, `structure_title`,
   `structure_desc`, `structure_icon`, `structure_locked`, `structure_count`) VALUES
('video', 'system', '999', '', 'System', '', '', 0, 0);

UPDATE `cot_structure` SET `structure_count` = (SELECT COUNT(*) FROM `cot_videos` WHERE `vid_cat`='system')
	WHERE `structure_code`='system';