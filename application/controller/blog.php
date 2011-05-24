<?php

/**
 * Template created by Gregory Chris
 */

load_model('blog');

$items = get_blog_items();

load_view('header');
load_view('blog', array('items'=>$items));
load_view('footer');