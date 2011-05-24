<?php

/**
 * Template created by Gregory Chris
 */

function get_last_version_check() {
    $res = $GLOBALS['db']->query('select `last_check` from `version` limit 1')->fetch();
    return !empty($res) ? $res['last_check'] : 0;
}


function get_current_version() {
    $res = $GLOBALS['db']->query('select `version` from `version` limit 1')->fetch();
    return !empty($res) ? $res['version'] : 0;
}

function update_last_version_check() {
    // update the check time
    $GLOBALS['db']->exec('update `version` set `last_check` = '.time());
}