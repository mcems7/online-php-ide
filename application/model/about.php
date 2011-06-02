<?php

/**
 * Template created by Gregory Chris
 */


function save_contact() {
    if (!empty($_POST['email']) && !empty($_POST['message'])) {
        // put the contact us form data to a file.
        // the LOG directory is chosed, because it's not being added to the source code
        if (($f = @fopen(_DIR_ROOT . '/log/contact.txt', 'a+')) !== false) {
            @fputs($f, date('d.M.Y H:i') . ': CONTACT <email:' . htmlentities($_POST['email'], ENT_QUOTES, 'utf-8') . '>' .
                            "\r\n" . htmlentities($_POST['message'], ENT_QUOTES, 'utf-8') . "\r\n");


            $headers = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
            $headers .= 'From: Online-PHP.com <admin@online-php.com>' . "\r\n";

            @mail('www.online.php@gmail.com', 'Contact from online-php.com', 
                    '<b>'.htmlentities($_POST['email'], ENT_QUOTES, 'utf-8')."</b>\r\n<br>".
                    htmlentities($_POST['message'], ENT_QUOTES, 'utf-8')."\r\n<br>".
                    '<br><br><a href="http://online-php.com/">Online-PHP</a>', $headers);

            @fclose($f);
        }
    }
}
