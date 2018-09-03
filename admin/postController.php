<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 9/3/18
 * Time: 1:50 PM
 */

require_once 'safeEnvironmentInitialization.php';
//--------------------- if user authorized let's proceed... ------------------------------------------------------------

function resetDraft() {
    unset($_SESSION['answertype']);
    unset($_SESSION['message']);
    unset($_SESSION['saveduserinput']);
}

if (isset($_GET['resetall']) && $_GET['resetall'] === 'true') {
    resetDraft();
    header("Location: adminview.php");
    exit;
} else {
    unset($_GET['resetall']);
}

if (is_numeric($_POST['category']) && !empty($_POST['title']) && !empty($_POST['body'])) {
    //if category set correct, title and body also not empty, let write article to Database:
} else {
    //return error with description what's wrong, and saved user input!!!, so it's not necessary to retype all content from scratch:
    switch (true) {
        case (!is_numeric($_POST['category'])):
            $_SESSION['answertype'] = 'ERROR';
            $_SESSION['message'] = 'Wrong category.';
            $_SESSION['saveduserinput'] = json_encode($_POST);
            break;
        case (empty($_POST['title'])):
            $_SESSION['answertype'] = 'ERROR';
            $_SESSION['message'] = 'Empty title.';
            $_SESSION['saveduserinput'] = json_encode($_POST);
            break;
        case (empty($_POST['body'])):
            $_SESSION['answertype'] = 'ERROR';
            $_SESSION['message'] = 'Empty body.';
            $_SESSION['saveduserinput'] = json_encode($_POST);
            break;
        default:
            echo 'Something really went wrong...';
    }

    header("Location: adminview.php");
    exit;
}