<?php defined('COT_CODE') or die('Wrong URL');
/* ====================
[BEGIN_COT_EXT]
Hooks=module
[END_COT_EXT]
==================== */

// Environment setup
define('COT_VIDEO', TRUE);
$env['location'] = 'video';

require_once cot_incfile('video', 'module');

$out['subtitle'] = $L['vid_all'];

require_once $cfg['system_dir'].'/header.php';

$t = new XTemplate(cot_tplfile('video', 'module'));

list($pg, $d, $durl) = cot_import_pagenav('d', $cfg['video']['perpage']);

$vid_res = $db->query("SELECT v.*, u.*
	FROM $db_videos AS v
		LEFT JOIN $db_users AS u ON v.vid_user = u.user_id
	ORDER BY vid_order, vid_id DESC
	LIMIT $d, {$cfg['video']['perpage']}");

foreach ($vid_res->fetchAll() as $vid)
{
	$t->assign(video_tpltags($vid, 'VIDEO_ROW_', $cfg['video']['main_width'], $cfg['video']['main_height']));
	$t->parse('MAIN.VIDEO_ROW');
}

$totalitems = $db->query("SELECT COUNT(*) FROM $db_videos")->fetchColumn();

$pagenav = cot_pagenav('video', '', $d, $totalitems, $cfg['video']['perpage'], 'd');

$t->assign(array(
	'VIDEO_PAGENAV'     => $pagenav['main'],
	'VIDEO_PAGEPREV'    => $pagenav['prev'],
	'VIDEO_PAGENEXT'    => $pagenav['next']
));

$t->parse();
$t->out();

require_once $cfg['system_dir'].'/footer.php';
