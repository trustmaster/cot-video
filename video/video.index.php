<?php defined('COT_CODE') or die('Wrong URL');
/* ====================
[BEGIN_COT_EXT]
Hooks=index.tags
Tags=index.tpl:{INDEX_VIDEO}
[END_COT_EXT]
==================== */

require_once cot_incfile('video', 'module');

// Get and display 3 latest vids
$tt = new XTemplate(cot_tplfile('video.index', 'module'));

$vid_res = $db->query("SELECT v.*, u.*
	FROM $db_videos AS v
		LEFT JOIN $db_users AS u ON v.vid_user = u.user_id
	ORDER BY vid_order, vid_id DESC
	LIMIT {$cfg['video']['perindex']}");

foreach ($vid_res->fetchAll() as $vid)
{
	$tt->assign(video_tpltags($vid, 'VIDEO_ROW_', $cfg['video']['index_width'], $cfg['video']['index_height']));
	$tt->parse('MAIN.VIDEO_ROW');
}

$tt->parse();
$t->assign('INDEX_VIDEO', $tt->text());
