<?php

/**
 * Template created by Gregory Chris
 */

load_model('about');

$content = get_about_content();

load_view('header');
load_view('about', array('content'=>$content));
load_view('footer');