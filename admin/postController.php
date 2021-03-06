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
require_once '..'.DS.'libphp'.DS.'Image.Class.php';
require_once '..'.DS.'libphp'.DS.'simplePDO.Class.php';

//TODO: move to separate config-file
$db = new simplePDO('essent.mysql.tools', 'essent_db', '2XxMUpHE', 'essent_db');

$postcontroller = new PostController($db);

//we expect GET parameters for routine user actions, such as open specified article for edit, reset draft etc...
$postcontroller->routineHandler($_GET);

//we expect POST (and OPTIONALLY FILES) parameters only when user submits new (or edited) article/record
$postcontroller->existingEditedPostHandler($_POST, $_FILES);

//we expect POST (and OPTIONALLY FILES) parameters only when user submits new (or edited) article/record
$postcontroller->newPostHandler($_POST, $_FILES);

//And finally, catch errors and SAVE USER DATA if necessary:
$postcontroller->errorHandler($_POST);

//When all methods have done their job - we return back to Admin Panel
header("Location: adminview.php");
exit;