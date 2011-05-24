<?php

/**
 * Template created by Gregory Chris
 */
if ($_SERVER['REQUEST_METHOD'] == 'POST' &&
        !empty($_POST['username']) && !empty($_POST['password'])) {
    
    load_model('admin/funcs');
    $user = get_user($_POST['username'], $_POST['password']);
    if (!empty($user)) {
        $_SESSION['user'] = $user;
    }
}


header('Location: ' . $_SERVER['HTTP_REFERER']);
die();
