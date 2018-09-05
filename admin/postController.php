<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 9/3/18
 * Time: 1:50 PM
 */

require_once 'safeEnvironmentInitialization.php';
//--------------------- if user authorized let's proceed... ------------------------------------------------------------

define('DS', DIRECTORY_SEPARATOR);
require_once '..'.DS.'libphp'.DS.'Article.Class.php';
require_once '..'.DS.'libphp'.DS.'PostController.Class.php';
require_once '..'.DS.'libphp'.DS.'db.class.php';

$db = new DB('essent.mysql.tools', 'essent_db', '2XxMUpHE', 'essent_db');
$postcontroller = new PostController($db);

//we expect GET parameters for routine user actions, such as open specified article for edit, reset draft etc...
$postcontroller->routineHandler($_GET);

//we expect POST parameters only when user submits new (or edited) article/record
$postcontroller->existingEditedPostHandler($_POST);

//we expect POST parameters only when user submits new (or edited) article/record
$postcontroller->newPostHandler($_POST);

//When all methods have done their job - we return back to Admin Panel
header("Location: adminview.php");
exit;