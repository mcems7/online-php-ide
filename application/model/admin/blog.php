<?php

/**
 * Template created by Gregory Chris
 */


function get_items($blog_id = -1) {
    
    $sql = 'select * from `posts` ';
    
    if (is_numeric($blog_id) && $blog_id > 0) {
        // load specific blog item
        $sql.= 'where `id` = '.$blog_id;
    }
    
    $sql.= ' order by `date_created` desc';
    
    $res = $GLOBALS['db']->query($sql)->fetchAll();
    
    if (is_numeric($blog_id) && $blog_id > 0) {
        return empty($res) ? array() : $res[0];
    } else {
        return $res;
    }
}


function update_post($post_id, $data) {
    $stmt = $GLOBALS['db']->prepare('
        update `posts` set 
            `title` = ?, 
            `content` = ?, 
            `date_created` = ?, 
            `author_name` = ?, 
            `author_email` = ?
        where `id` = ?');
    $stmt->execute(array(
        $data['title'], $data['content'], time(), 'Gregory C.', 'www.online.php@gmail.com', $post_id
    ));
}

function delete_post($post_id) {
    $GLOBALS['db']->exec('delete from `posts` where `id` = '.$post_id);
}

function add_post($data) {
    $stmt = $GLOBALS['db']->prepare('
        insert into `posts` (`title`, `content`, `date_created`, `author_name`, `author_email`) 
        values (?, ?, ?, ?, ?)');
    $stmt->execute(array(
        $data['title'], $data['content'], time(), 'Gregory C.', 'www.online.php@gmail.com'
    ));
}
