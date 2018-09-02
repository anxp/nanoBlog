<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 9/2/18
 * Time: 12:17 AM
 */
ini_set('session.save_path', '/Users/andrey/Sites/sessions');
session_start();

//check if role is not admin then redirect to login page
if($_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}
//--------------------- if user authorized let's proceed... ------------------------------------------------------------
define('DS', DIRECTORY_SEPARATOR);
require_once 'contentController.php';
require_once '..'.DS.'libphp'.DS.'db.class.php';
$db = new DB('essent.mysql.tools', 'essent_db', '2XxMUpHE', 'essent_db');
$categoriesArr = contentController::getCategories($db);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Admin View</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="./styles/admstyle.css">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 mb-2">
            <nav class="navbar navbar-expand-md navbar-dark bg-dark">

                <a class="navbar-brand" href="#">nanoBlog Admin:</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarText">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item active">
                            <a class="nav-link" href="#">Контент<span class="sr-only">(current)</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Пользователи и роли</a>
                        </li>
                    </ul>
                    <span class="navbar-text">UID: <?=htmlspecialchars($_SESSION['user_ID'])?> | Логин: <?=htmlspecialchars($_SESSION['userLogin'])?> | Доступ: <?=htmlspecialchars($_SESSION['role'])?> | Выход</span>
                </div>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <div id="articleBlock" class="border border-primary">
                <div id="articleHeader" class="header"><b>Создайте новую запись</b></div>
                <form id="articleForm" class="p-1">
                    <div class="input-group" id="articleTopControls">
                        <input id="articleTitle" type="text" class="form-control" style="width: 50%" placeholder="Введите заголовок...">
                        <select class="custom-select" id="articleCategory">
                            <option selected>Категория</option>
                            <?php foreach($categoriesArr as $key => $element): ?>
                            <option value="<?=$key?>"><?=$element?></option>
                            <?php endforeach; ?>
                        </select>
                        <select class="custom-select" id="articleStatus">
                            <option selected>Черновик</option>
                            <option value="1">Опубликовано</option>
                        </select>
                        <div class="input-group-append input-group-prepend" >
                            <button id="articleSave" class="btn btn-success" type="submit">Сохранить</button>
                        </div>
                    </div>

                    <div id="articleBody" class="form-group mb-0">
                        <textarea class="form-control" id="articleBodyText" rows="10"></textarea>
                    </div>
                </form>
                <div id="articleFooter" class="footer">Здесь можно будет добавить ключевые слова и картинку</div>
            </div>
        </div>
        <div class="col-md-4">
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <nav>
                <ul class="pagination">
                    <li class="page-item">
                        <a class="page-link" href="#">Previous</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">1</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">2</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">3</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">4</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">5</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script>
    function toggleFormAndFooter() {
        //getting main form of editor and footer
        $content = $("#articleForm");
        $footer = $("#articleFooter");
        //open up the content needed - toggle the slide- if visible, slide up, if not slidedown.
        $content.slideToggle(500, function () {
            //execute this after slideToggle is done
            $footer.slideToggle(500, function () {
                //execute this after slideToggle is done
            });
        });
    }

    $("#articleHeader").click(function () {
        toggleFormAndFooter();
    });

</script>
</body>
</html>
