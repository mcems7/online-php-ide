<?php

/**
 * Template created by Gregory Chris
 */


function get_tutorial_items() {

    return $GLOBALS['db']->query('
        select *
        from `tutorials`
        order by `date_created` desc')->fetchAll();

}

function get_tutorial_item_by_id($tut_id) {
    $stmt = $GLOBALS['db']->prepare('
        select *
        from `tutorials`
        where `id` = ?');
    $stmt->execute(array($tut_id));
    $row = $stmt->fetch();
    return empty($row) ? array() : $row;
}

function get_tutorial_item_by_title($tut_title) {
    $stmt = $GLOBALS['db']->prepare('
        select *
        from `tutorials`
        where `title` like ?');
    $stmt->execute(array(urldecode($GLOBALS['url_params'][0])));
    $row = $stmt->fetch();
    return empty($row) ? array() : $row;
}
