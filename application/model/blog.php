<?php

/**
 * Template created by Gregory Chris
 */


function get_blog_items() {
    
    return $GLOBALS['db']->query('
        select p.*, count(pc.`id`) total_comments
        from `posts` p
            left join `posts_comments` pc ON pc.`post_id` = p.`id`
        group by p.`id`
        order by p.`date_created` desc')->fetchAll();
    
}