<?php defined('COT_CODE') or die('Wrong URL');
/* ====================
[BEGIN_COT_EXT]
Hooks=module
[END_COT_EXT]
==================== */

// Environment setup
define('COT_VIDEO', TRUE);
$env['location'] = 'video';

$c = cot_import('c', 'G', 'TXT');
if(!empty($c) && !isset($structure['video'][$c]))
{
	cot_die_message(404);
}

if(empty($c) && isset($structure['video']['system']))
{
	$c = empty($c) ? 'system' : $c;
}

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('video', empty($c) ? 'any' : $c);
cot_block($usr['auth_read']);

require_once cot_incfile('video', 'module');

$out['subtitle'] = $L['vid_all'];

require_once $cfg['system_dir'].'/header.php';

$t = new XTemplate(cot_tplfile(array('video', $structure['video'][$c]['tpl']), 'module'));

list($pg, $d, $durl) = cot_import_pagenav('d', $cfg['video']['perpage']);

$vid_cats = video_category_children($c);
if($vid_cats)
{
	foreach($vid_cats as $vid_cat)
	{
		$t->assign(video_cat_tpltags($vid_cat, 'VIDEO_CATS_'));
		$t->parse('MAIN.VIDEO_CATS');
	}
}

$vid_res = $db->query("SELECT v.*, u.*
	FROM $db_videos AS v
		LEFT JOIN $db_users AS u ON v.vid_user = u.user_id
	WHERE v.vid_cat=?
	ORDER BY v.vid_order, v.vid_id DESC
	LIMIT $d, {$cfg['video']['perpage']}", $c);

foreach ($vid_res->fetchAll() as $vid)
{
	$t->assign(video_tpltags($vid, 'VIDEO_ROW_', $cfg['video']['main_width'], $cfg['video']['main_height']));
	$t->parse('MAIN.VIDEO_ROW');
}

$totalitems = $db->query("SELECT COUNT(*) FROM $db_videos WHERE vid_cat=?", $c)->fetchColumn();

$pagenav = cot_pagenav('video', '', $d, $totalitems, $cfg['video']['perpage'], 'd');

if(!empty($c))
{
	$video_crumbs = array_merge(array(array(cot_url('video'), $L['vid_title'])), cot_structure_buildpath('video', $c));
}

$video_has_children = $vid_cats ? true : false;
$video_has_items = $totalitems > 0 ? true : false;

$t->assign(array(
	'VIDEO_PATH' 			=> !empty($c) ? cot_breadcrumbs($video_crumbs, false, true) : $L['vid_all'],
	'VIDEO_HAS_CHILDREN'	=> $video_has_children,
	'VIDEO_HAS_ITEMS'		=> $video_has_items,
	'VIDEO_PAGENAV'			=> $pagenav['main'],
	'VIDEO_PAGEPREV'		=> $pagenav['prev'],
	'VIDEO_PAGENEXT'		=> $pagenav['next']
));

$t->parse();
$t->out();

require_once $cfg['system_dir'].'/footer.php';
