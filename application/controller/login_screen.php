<?php

/**
 * Template created by Gregory Chris
 */
load_model('version');

load_view('login_screen', array('version'=>  get_current_version()));
load_view('footer');
