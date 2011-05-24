<?php

/**
 * Template created by Gregory Chris
 */

function get_user($username, $password) {
    
    /*
    $stmt = $GLOBALS['db']->prepare('
            insert into `admin_users` (`username`, `password`, `email`)
            values (?, ?, ?);
        ');
    */
    $stmt = $GLOBALS['db']->prepare('
        select * from `admin_users` 
        where `username` = ? and `password` = ? limit 1');
    $stmt->execute(array($username, md5($password)));
    $row = $stmt->fetch();
    return empty($row) ? array() : $row;
}