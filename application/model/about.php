<?php

/**
 * Template created by Gregory Chris
 */


function get_about_content() {
    
    return '
<h3>First of all</h3>
What you are going to do, how, and how will you use it - simple steps:

1. Configure your application so that all the URLs will lead to one script file - index.php
2. The main file index.php parses the REQUEST_URI (i.e. the URL) to determine the request
3. Load scripts and files relevant to the request.

<h3>Redirect</h3> 
all traffic to index.php
create file named ".hraccess" in your root folder, and put the next content:

<ul>
    <li>1. Configure your application so that all the URLs will lead to one script file - index.php</li>
    <li>2. The main file index.php parses the REQUEST_URI (i.e. the URL) to determine the request</li>
    <li>3. Load scripts and files relevant to the request.</li>
    
<h3>end</h3>

';
    
}