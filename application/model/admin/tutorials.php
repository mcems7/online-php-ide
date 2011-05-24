<?php

/**
 * Template created by Gregory Chris
 */


function get_items($tut_id = -1) {
    
    $sql = 'select * from `tutorials` ';
    
    if (is_numeric($tut_id) && $tut_id > 0) {
        // load specific blog item
        $sql.= 'where `id` = '.$tut_id;
    }
    
    $sql.= ' order by `date_created` desc';
    
    $res = $GLOBALS['db']->query($sql)->fetchAll();
    
    if (is_numeric($tut_id) && $tut_id > 0) {
        return empty($res) ? array() : $res[0];
    } else {
        return $res;
    }
}


function update_post($post_id, $data) {
    $stmt = $GLOBALS['db']->prepare('
        update `tutorials` set 
            `title` = ?, 
            `intro` = ?,
            `content` = ?, 
            `date_created` = ?
        where `id` = ?');
    $stmt->execute(array(
        $data['title'], $data['intro'], $data['content'], time(), $post_id
    ));
}

function delete_post($post_id) {
    $GLOBALS['db']->exec('delete from `tutorials` where `id` = '.$post_id);
}

function add_post($data) {
    $stmt = $GLOBALS['db']->prepare('
        insert into `tutorials` (`title`, `intro`, `content`, `date_created`) 
        values (?, ?, ?, ?)');
    $stmt->execute(array(
        $data['title'], $data['intro'], $data['content'], time()
    ));
}
