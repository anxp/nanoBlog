<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 9/1/18
 * Time: 4:58 PM
 */
ini_set('session.save_path', '/Users/andrey/Sites/sessions');

define('DS', DIRECTORY_SEPARATOR);
include '..'.DS.'libphp'.DS.'db.class.php';

function cleanString($str) {
    $str = trim($str);
    $str = stripslashes($str);
    $str = strip_tags($str);
    return $str;
}

//$frontEndData is assoc array which stores all data user submitted in webform,
//generally, these are untrusted data, so we need check it accordingly
$frontEndData = json_decode(cleanString(file_get_contents("php://input")), true);

//in this array we will store server response - message (maybe error message)...
$serverResponseArr = array(
    'message' => '',
    'url' => '',
);

if (isset($frontEndData['username']) && isset($frontEndData['userpass'])) {
    $username = $frontEndData['username'];
    $userpass = $frontEndData['userpass'];
    $chkboxstatus = intval($frontEndData['checkbox']);
} else {
    $serverResponseArr['message'] = 'LoginError';
    echo json_encode($serverResponseArr);
    exit;
}

//Create new DB Connection
//TODO: in this file, we only check account for existing, so for more secure, login to DataBase must be READ-ONLY
$db = new DB('essent.mysql.tools', 'essent_db', '2XxMUpHE', 'essent_db');

$username = $db->escape($username);
$userpass = $db->escape($userpass);

$sql = "SELECT `user_ID`,`login`,`role` FROM users WHERE login = '{$username}' AND pass = '{$userpass}';";
$sqlResponse = $db->query($sql);

if(empty($sqlResponse)) {
    //if $sqlResponse contains empty array, this mean no user found in database, so we return LoginError message to frontend
    $serverResponseArr['message'] = 'LoginError';
    echo json_encode($serverResponseArr);
    exit;
} else {
    //at this point we have user passed AUTHENTICATION, and he(she) has role. role = admin || editor or maybe something else
    //let store his/her user_ID, login and role in session, for use on next stage -> AUTHORIZATION
    //also, let's send to frontend message 'Continue' with URL to admin panel - adminview.php:

    session_start();

    $serverResponseArr['message'] = 'Continue';
    $serverResponseArr['url'] = 'adminview.php';

    $_SESSION['user_ID'] = $sqlResponse[0]['user_ID'];
    $_SESSION['userLogin'] = $sqlResponse[0]['login'];
    $_SESSION['role'] = $sqlResponse[0]['role'];

    echo json_encode($serverResponseArr);

    exit;
}