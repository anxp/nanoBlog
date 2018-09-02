<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 9/2/18
 * Time: 10:00 PM
 */
function cleanString($str) {
    $str = trim($str);
    $str = stripslashes($str);
    $str = strip_tags($str);
    return $str;
}