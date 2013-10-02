<?php defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('extrafields');
require_once cot_incfile('forms');

require_once cot_langfile('video', 'module');

if (!isset($GLOBALS['db_videos'])) $GLOBALS['db_videos'] = $GLOBALS['db_x'] . 'videos';

global $db_videos, $cot_extrafields, $structure;

$cot_extrafields[$db_videos] = (!empty($cot_extrafields[$db_videos]))	? $cot_extrafields[$db_videos] : array();

$structure['video'] = (is_array($structure['video'])) ? $structure['video'] : array();
$video_has_categories = count($structure['video']) > 0 ? true : false;

/**
 * Adds a new video entry to the database
 * @param  array   $data Video data
 * @return integer       New entry ID or FALSE on error
 */
function video_add($data)
{
	global $db, $db_structure, $db_videos;

	if (cot_error_found())
	{
		return false;
	}

	if ($db->insert($db_videos, $data))
	{
		$id = $db->lastInsertId();
	}
	else
	{
		$id = false;
	}

	if (!empty($data['vid_cat']))
	{
		$db->query("UPDATE $db_structure SET structure_count=structure_count+1 WHERE structure_code = ? AND structure_area = 'video'", $data['vid_cat']);
	}

	return $id;
}

/**
 * Returns permissions for a video category.
 * @param  string $cat Category code
 * @return array       Permissions array with keys: 'auth_read', 'auth_write', 'isadmin'
 */
function video_auth($cat = null)
{
	if (empty($cat))
	{
		$cat = 'any';
	}
	$auth = array();
	list($auth['auth_read'], $auth['auth_write'], $auth['isadmin']) = cot_auth('video', $cat, 'RWA');
	return $auth;
}

/**
 * Removes a video
 * @param  integer $id Video ID
 * @return boolean     TRUE on success, FALSE on error
 */
function video_delete($id)
{
	global $db, $db_structure, $db_videos;

	if (!is_numeric($id) || $id <= 0)
	{
		return false;
	}
	$id = (int)$id;

	$row_vid = $db->query("SELECT * FROM $db_videos WHERE vid_id = ?", $id)->fetch();

	if (!empty($row_vid['vid_cat']))
	{
		$db->query("UPDATE $db_structure SET structure_count=structure_count-1 WHERE structure_code = ? AND structure_area = 'video'", $row_vid['vid_cat']);
	}

	return $db->delete($db_videos, "vid_id = ?", $id) > 0;
}

/**
 * Imports video data from request parameters.
 * @param  string $source Source request method for parameters
 * @param  array  $rpage  Existing video data from database
 * @param  string $prefix Input name prefix
 * @param  array  $auth   Permissions array
 * @return array          Video data
 */
function video_import($source = 'POST', $data = array(), $prefix = 'rvid', $auth = array())
{
	global $cot_extrafields, $db_videos, $sys, $usr;

	if (count($auth) === 0)
	{
		$auth = video_auth($data['vid_cat']);
	}

	if ($source === 'D' || $source === 'DIRECT')
	{
		// A trick so we don't have to affect every line below
		global $_PATCH;
		$_PATCH = $data;
		$source = 'PATCH';
	}

	$data['vid_cat']    = cot_import($prefix . 'cat', $source, 'TXT');
	$data['vid_title']  = cot_import($prefix . 'title', $source, 'TXT');
	$data['vid_source'] = cot_import($prefix . 'source', $source, 'ALP');
	$data['vid_code']   = cot_import($prefix . 'code', $source, 'ALP');
	$data['vid_order']  = cot_import($prefix . 'order', $source, 'INT');
	$data['vid_added']  = isset($data['vid_added']) ? $data['vid_added'] : $sys['now'];
	$data['vid_user']   = isset($data['vid_user']) ? $data['vid_user'] : $usr['id'];

	// Extra fields
	foreach ($cot_extrafields[$db_videos] as $exfld)
	{
		$data['vid_'.$exfld['field_name']] = cot_import_extrafields($prefix . $exfld['field_name'], $exfld, $source, $data['vid_'.$exfld['field_name']]);
	}

	return $data;
}

/**
 * Assigns form tags for a video.
 * @param  array  $row          Database row
 * @param  string $prefix       Tag prefix
 * @param  string $input_prefix Input name prefix
 * @return array                Array of tags for assignment
 */
function video_tplform($row, $prefix, $input_prefix = 'rvid')
{
	global $cot_extrafields, $db_videos, $L;

	$tpltags = array(
		$prefix . 'CAT' => cot_selectbox_structure('video', $row['vid_cat'], $input_prefix . 'cat'),
		$prefix . 'TITLE' => cot_inputbox('text', $input_prefix . 'title', $row['vid_title'], array('size' => '32', 'maxlength' => '255')),
		$prefix . 'SOURCE' => cot_selectbox($row['vid_source'], $input_prefix . 'source', array('youtube', 'vimeo'), array('Youtube', 'Vimeo'), false),
		$prefix . 'CODE' => cot_inputbox('text', $input_prefix . 'code', $row['vid_code'], array('size' => '32', 'maxlength' => '64')),
		$prefix . 'ORDER' => cot_selectbox($row['vid_order'], $input_prefix . 'order', range(1,100), range(1, 100), false)
	);

	// Extra fields
	foreach ($cot_extrafields[$db_videos] as $exfld)
	{
		$uname = strtoupper($exfld['field_name']);
		$exfld_val = cot_build_extrafields($input_prefix . $exfld['field_name'], $exfld, $row['vid_'.$exfld['field_name']]);
		$exfld_title = isset($L['vid_'.$exfld['field_name'].'_title']) ?  $L['vid_'.$exfld['field_name'].'_title'] : $exfld['field_description'];

		$tpltags[$prefix . $uname] = $exfld_val;
		$tpltags[$prefix . $uname . '_TITLE'] = $exfld_title;
	}

	return $tpltags;
}

/**
 * Generates TPL tags for a video entry.
 * @param  array   $row           Database row
 * @param  string  $prefix        Tag prefix
 * @param  integer $player_width  Video player width
 * @param  integer $player_height Video player height
 * @param  boolean $can_edit      Permissions to edit/delete entry
 * @return array                  Array of tags for assignment
 */
function video_tpltags($row, $prefix = 'VIDEO_ROW_', $player_width = 853, $player_height = 480, $can_edit = false)
{
	global $cot_extrafields, $db_videos, $structure, $L;

	$catpath = cot_structure_buildpath('video', $row['vid_cat']);
	$breadcrumbs = cot_breadcrumbs($catpath);

	if ($row['vid_source'] === 'youtube')
	{
		$player = '<iframe width="'.$player_width.'" height="'.$player_height.'" src="http://www.youtube.com/embed/'.$row['vid_code'].'?rel=0" frameborder="0" allowfullscreen></iframe>';
		$url = 'http://www.youtube.com/watch?v=' . $row['vid_code'];
	}
	elseif ($row['vid_source'] === 'vimeo')
	{
		$player = '<iframe width="'.$player_width.'" height="'.$player_height.'" src="http://player.vimeo.com/video/'.$row['vid_code'].'" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
		$url = 'http://vimeo.com/' . $row['vid_code'];
	}
	else
	{
		$player = '';
		$url = '';
	}

	$tpltags = array(
		$prefix . 'ID'          => $row['vid_id'],
		$prefix . 'CAT'         => $row['vid_cat'],
		$prefix . 'CAT_TITLE'   => htmlspecialchars($structure['video'][$row['vid_cat']]['title']),
		$prefix . 'CAT_PATH'    => $breadcrumbs,
		$prefix . 'TITLE'       => htmlspecialchars($row['vid_title']),
		$prefix . 'SOURCE'      => $row['vid_source'],
		$prefix . 'CODE'        => $row['vid_code'],
		$prefix . 'ADDED'       => cot_date('datetime_medium', $row['vid_added']),
		$prefix . 'ADDED_STAMP' => $row['vid_added'],
		$prefix . 'PLAYER'      => $player,
		$prefix . 'URL'         => $url
	);

	if ($can_edit)
	{
		$edit_url = cot_url('video', 'm=edit&id=' . $row['vid_id']);
		$tpltags['EDIT_URL'] = $edit_url;
		$tpltags['EDIT'] = cot_rc_link($edit_url, $L['Edit']);
		$delete_url = cot_url('video', 'm=delete&id=' . $row['vid_id']);
		$delete_confirm_url = cot_confirm_url($delete_url);
		$tpltags['DELETE_URL'] = $delete_confirm_url;
		$tpltags['DELETE'] = cot_rc_link($delete_confirm_url, $L['Delete']);
	}

	// Extrafields
	if (isset($cot_extrafields[$db_videos]))
	{
		foreach ($cot_extrafields[$db_videos] as $exfld)
		{
			$tag = mb_strtoupper($exfld['field_name']);
			$tpltags[$tag.'_TITLE'] = isset($L['vid_'.$exfld['field_name'].'_title']) ?  $L['vid_'.$exfld['field_name'].'_title'] : $exfld['field_description'];
			$tpltags[$tag] = cot_build_extrafields_data('video', $exfld, $row['vid_'.$exfld['field_name']]);
			$tpltags[$tag.'_VALUE'] = $row['vid_'.$exfld['field_name']];
		}
	}

	return $tpltags;
}

/**
 * Updates video entry data.
 * @param  integer $id   Video ID
 * @param  array   $data Video data
 * @return boolean       TRUE on success, FALSE on error
 */
function video_update($id, $data)
{
	global $cache, $db, $db_structure, $db_videos;

	if (cot_error_found())
	{
		return false;
	}

	$row_vid = $db->query("SELECT * FROM $db_videos WHERE vid_id = ?", $id)->fetch();

	if ($row_vid['vid_cat'] != $data['vid_cat'])
	{
		!empty($row_vid['vid_cat']) && $db->query("UPDATE $db_structure SET structure_count=structure_count-1 WHERE structure_code = ? AND structure_area = 'video'", $row_vid['vid_cat']);
		!empty($data['vid_cat']) && $db->query("UPDATE $db_structure SET structure_count=structure_count+1 WHERE structure_code = ? AND structure_area = 'video'", $data['vid_cat']);
	}

	$cache && $cache->db->remove('structure', 'system');

	if (!$db->update($db_videos, $data, 'vid_id = ?', $id))
	{
		return false;
	}

	return true;
}

/**
 * Validates video data.
 * @param  array   $data         Imported video data
 * @param  string  $input_prefix Input name prefix
 * @return boolean               TRUE if validation is passed or FALSE if errors were found
 */
function video_validate($data, $input_prefix = 'rvid')
{
	cot_check(mb_strlen($data['vid_title']) < 2, 'vid_err_titletooshort', $input_prefix . 'title');
	cot_check(empty($data['vid_code']), 'vid_err_emptycode', $input_prefix . 'code');
	return !cot_error_found();
}

/**
* Generate tpl tags for a category
* @param string $cat Category code
* @param string $prefix TPL tag prefix
* @return array Array of tags for assignment
*/
function video_cat_tpltags($cat, $prefix = 'VIDEO_CATS_')
{
	global $structure;
	return array(
		$prefix.'CODE' => htmlspecialchars($cat),
		$prefix.'TITLE' => htmlspecialchars($structure['video'][$cat]['title']),
		$prefix.'DESC' => htmlspecialchars($structure['video'][$cat]['desc']),
		$prefix.'ICON' => htmlspecialchars($structure['video'][$cat]['icon']),
		$prefix.'LOCKED' => (bool)$structure['video'][$cat]['locked'],
		$prefix.'COUNT' => (int)$structure['video'][$cat]['count'],
		$prefix.'ID' => (int)$structure['video'][$cat]['id'],
		$prefix.'PATH' => cot_breadcrumbs(cot_structure_buildpath('video', $cat), false),
		$prefix.'URL' => cot_url('video', 'c='.$cat)
	);
}

/**
* Get children category codes for inputed category
* @param string $cat The category to get the children for. 'system' will return the top level categories
* @return mixed Children category codes or false if none
*/
function video_category_children($cat = 'system')
{
	global $structure;

	$cats = array();
	if($cat == 'system')
	{
		// Grab top level categories
		foreach($structure['video'] as $code => $data)
		{
			if(mb_strpos($data['rpath'], '.') === false && $code != 'system')
			{
				$auth = video_auth($code);
				if($auth['auth_read'])
				{
					$cats[] = $code;
				}
			}
		}
	}
	else
	{
		$cats = cot_structure_children('video', $cat, false, false);
	}
	return count($cats) > 0 ? $cats : false;
}

/**
* Recounts videos in a category
* @param string $cat Category code
* @return int Count
*/
function cot_video_sync($cat)
{
	global $db, $db_videos;
	return (int)$db->query("SELECT COUNT(*) FROM $db_videos WHERE vid_cat=?", $cat)->fetchColumn();
}
