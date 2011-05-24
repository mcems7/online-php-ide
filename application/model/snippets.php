<?php

/**
 * Template created by Gregory Chris
 */


function get_snippet_items() {

    return $GLOBALS['db']->query('
        select s.*, l.`name` language
        from `snippets` s
			left join `languages` l on l.`id` = s.`language_id`
		where s.`approved` = 1
        order by s.`date_created` desc')->fetchAll();

}

function get_snippet_items_by_tag($tag) {
	if (empty($tag)) {
		return get_snippet_items();
	} else {

		$stmt = $GLOBALS['db']->prepare('
			select s.*, l.`name` language
			from `snippets` s
				left join `languages` l on l.`id` = s.`language_id`
			where s.`approved` = 1 and s.`tags` like ?
			order by s.`date_created` desc');
		$stmt->execute(array('%'.urldecode($tag).'%'));
				
		return $stmt->fetchAll();
	}

}


function get_snippet_item($item_id) {
    $stmt = $GLOBALS['db']->prepare('
		select s.*, l.`name` language
        from `snippets` s
			left join `languages` l on l.`id` = s.`language_id`
		where s.`approved` = 1 and s.`id` = ?');
	$stmt->execute(array($item_id));
	return $stmt->fetch();
}

function get_snippet_item_by_name($item_name) {
    $item_name = urldecode($item_name);
    $stmt = $GLOBALS['db']->prepare('
		select s.*, l.`name` language
        from `snippets` s
			left join `languages` l on l.`id` = s.`language_id`
		where s.`approved` = 1 and s.`name` like ?');
	$stmt->execute(array('%'.$item_name.'%'));
	$rows = $stmt->fetchAll();
	return empty($rows) ? array() : $rows;
}



function get_snippet_item_by_language($item_language) {
    $item_language = urldecode($item_language);
    $stmt = $GLOBALS['db']->prepare('
		select s.*, l.`name` language
        from `snippets` s
			left join `languages` l on l.`id` = s.`language_id`
		where s.`approved` = 1 and l.`name` like ?');
	$stmt->execute(array('%'.$item_language.'%'));
	$rows = $stmt->fetchAll();
	return empty($rows) ? array() : $rows;
}



// limit string to the nearest ' ' (space) char.
function limit_string($str, $len) {
    while ($str[$len] != ' ' && $len > 0) $len--;
    return rtrim(substr($str, 0, $len));
}


function trim_and_get_link(&$tag, $key) {
    $tag = '<a href="'._HTTP_ROOT.'/snippets/tag/'.urlencode(trim($tag)).'/">'.trim($tag).'</a>';
}

function show_tags($tags) {
    $tags_arr = explode(',', $tags);
    array_walk($tags_arr, 'trim_and_get_link');
    return implode(', ', $tags_arr);
}


function output_code($code) {
    return highlight_string($code, true);
}