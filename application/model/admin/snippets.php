<?php

/**
 * Template created by Gregory Chris
 */

function get_languages() {
    return $GLOBALS['db']->query('select * from `languages`')->fetchAll();
}


function get_items($tut_id = -1) {
    
    $sql = 'select * from `snippets` ';
    
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
        update `snippets` set 
	    `approved` = :approved,
            `name` = :name, 
            `description` = :desc,
            `code` = :code, 
            `tags` = :tags, 
            `language_id` = :lang, 
            `date_created` = :date
        where `id` = :id');
    $stmt->bindValue(':approved', ($data['approve'] == 'yes' ? '1' : '0'), PDO::PARAM_STR);
    $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
    $stmt->bindValue(':desc', $data['description'], PDO::PARAM_STR);
    $stmt->bindValue(':code', $data['code'], PDO::PARAM_STR);
    $stmt->bindValue(':tags', $data['tags'], PDO::PARAM_STR);
    $stmt->bindValue(':lang', $data['language_id'], PDO::PARAM_INT);
    $stmt->bindValue(':date', time(), PDO::PARAM_INT);
    $stmt->bindValue(':id', $post_id, PDO::PARAM_INT);
    $res = $stmt->execute();
    
}

function delete_post($post_id) {
    $GLOBALS['db']->exec('delete from `snippets` where `id` = '.$post_id);
}

function add_post($data) {
    $stmt = $GLOBALS['db']->prepare('
        insert into `snippets` (`name`, `description`, `code`, `tags`, `language_id`, `date_created`) 
        values (:name, :desc, :code, :tags, :lang, :date)');
    $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
    $stmt->bindValue(':desc', $data['description'], PDO::PARAM_STR);
    $stmt->bindValue(':code', $data['code'], PDO::PARAM_STR);
    $stmt->bindValue(':tags', $data['tags'], PDO::PARAM_STR);
    $stmt->bindValue(':lang', $data['language_id'], PDO::PARAM_INT);
    $stmt->bindValue(':date', time(), PDO::PARAM_INT);
    $stmt->execute();
}
