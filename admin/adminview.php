<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 9/2/18
 * Time: 12:17 AM
 */
require_once 'safeEnvironmentInitialization.php';
//--------------------- if user authorized let's proceed... ------------------------------------------------------------

if($_SESSION['answertype'] === 'ERROR') { //if we got an error from postController, let's handle it:
    $answerType = $_SESSION['answertype'];
    $errDescription = $_SESSION['message'];
    $savedUserInput = json_decode($_SESSION['saveduserinput'], true);
    //now, we have all content that user tried to submit last time:
    //$savedUserInput['title'] for TITLE, $savedUserInput['body'] for BODY, $savedUserInput['category'] for CATEGORY and so on...
} else {
    //if postController HAS NOT returned error, just explicitly declare variables as empties - it's better then if they not exists at all
    $answerType = '';
    $errDescription = '';
    $savedUserInput = '';
}

define('DS', DIRECTORY_SEPARATOR);
require_once '..'.DS.'libphp'.DS.'Article.Class.php';
require_once '..'.DS.'libphp'.DS.'Pagination.Class.php';
require_once '..'.DS.'libphp'.DS.'db.class.php';

$db = new DB('essent.mysql.tools', 'essent_db', '2XxMUpHE', 'essent_db');
$categoriesArr = Article::getCategories($db); //Load categories from DataBase to indexed array

$pagination = new Pagination($db);
$currentPageTOC = $pagination->getCurrentPageItems($_GET['page']); //TOC is Table Of Contents
$totalPagesNum = $pagination->getTotalPagesNumber();

var_dump($savedUserInput);
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
            <div id="articleBlock" class="border border-primary mb-2">
                <div id="articleHeader" class="header">

                    <!-- PHP Code Insertion -->
                    <!-- If ERROR - notify user and show error description, else just show standard welcome to create new article -->
                    <?php if ($answerType === 'ERROR') { ?>
                        <?= '<b>Ошибка: '.$errDescription.'</b> <a class="text-light" href="postController.php?resetall=true">Удалить все, не хочу сохранять</a>' ?>
                    <?php } else { ?>
                        <?= '<b>Нажмите, чтобы создать новую запись</b>' ?>
                    <?php } ?>
                    <!------------------------>

                </div>
                <form id="articleForm" class="p-1" method="post" action="postController.php">
                    <div class="input-group" id="articleTopControls">

                        <!-- PHP Code Insertion -->
                        <!-- If we have ERROR with error description = Empty title. - do nothing, else output title, saved from last page reload. -->
                        <?php if($answerType === 'ERROR' && $errDescription === 'Empty title.') { ?>
                            <input id="articleTitle" name="title" type="text" class="form-control" style="width: 50%" placeholder="Введите заголовок...">
                        <?php } else { ?>
                            <input value="<?= $savedUserInput['title'] ?>" id="articleTitle" name="title" type="text" class="form-control" style="width: 50%" placeholder="Введите заголовок...">
                        <?php } ?>
                        <!------------------------>

                        <select id="articleCategory" name="category" class="custom-select">

                            <!-- PHP Code Insertion -->
                            <!-- Формируем выпадающее меню категорий. Оно достаточно умное - запоминает указанную пользователем категорию, и если
                            сохранение в базу не состоялось и черновик вернулся на доработку, подставляется выбранная пользователем категория. -->

                            <?= (intval($savedUserInput['category']) === 0 ? '<option selected>Категория</option>' : '') ?>
                            <?php foreach($categoriesArr as $key => $element): ?>
                            <option <?= (intval($key) === intval($savedUserInput['category'])) ? 'selected' : '' ?> value="<?=$key?>"><?=$element?></option>
                            <?php endforeach; ?>
                            <!------------------------>

                        </select>

                        <select id="articleStatus" name="status" class="custom-select">

                            <!-- PHP Code Insertion -->
                            <!-- Формируем меню опубликовано/черновик. Здесь мы также запоминаем какой пункт выбрал пользователь,
                            и при необходимости подставляем это значение. Но в данном случае все гораздо проще чем с категориями,
                            так как содержимое этого меню мы не подтягиваем с БД -->
                            <option <?= (intval($savedUserInput['status']) === 0 ? 'selected' : '') ?> value="0">Черновик</option>
                            <option <?= (intval($savedUserInput['status']) === 1 ? 'selected' : '') ?> value="1">Опубликовано</option>
                            <!------------------------>

                        </select>
                        <div class="input-group-append input-group-prepend" >
                            <button id="articleSave" class="btn btn-success" type="submit">Сохранить</button>
                        </div>
                    </div>

                    <div id="articleBody" class="form-group mb-0">
                        <!-- если с телом записи ошибок нет, но есть другая ошибка, то вывести сохраненное тело записи -->
                        <textarea id="articleBodyText" name="body" class="form-control" rows="10" ><?php if ($answerType === 'ERROR' && $errDescription !== 'Empty body.') echo $savedUserInput['body']; ?></textarea>
                    </div>
                </form>
                <div id="articleFooter" class="footer">Здесь можно будет добавить ключевые слова и картинку</div>
            </div>
            <div class="d-flex justify-content-center">
            <nav>
                <ul class="pagination">
                    <!-- Вывод начального блока пагинации (кнопки Предыдущая и Первая) -->
                    <li class="page-item">
                        <a class="page-link" href="adminview.php?page=1">Первая</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="adminview.php?page=<?= (intval($_GET['page'])-1) < 0 ? 0 : (intval($_GET['page'])-1) ?>">Предыдущая</a>
                    </li>
                    <!------------------------------------------------------------------->

                    <!-- Вывод центрального блока пагинации (цифры) -->
                    <?php for($i=1; $i<=$totalPagesNum; $i++): ?>
                        <li class="page-item">
                                <?= '<a class="page-link" href="adminview.php?page='.$i.'">'.$i.'</a>' ?>
                        </li>
                    <?php endfor; ?>
                    <!------------------------------------------------>

                    <!-- Вывод конечного блока пагинации (кнопки Следующая и Последняя) -->
                    <li class="page-item">
                        <a class="page-link" href="adminview.php?page=<?= (intval($_GET['page'])+1) <= $totalPagesNum ? (intval($_GET['page'])+1) : $totalPagesNum ?>">Следующая</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="adminview.php?page=<?= $totalPagesNum ?>">Последняя</a>
                    </li>
                    <!-------------------------------------------------------------------->
                </ul>
            </nav>
            </div>
            <div class="border border-secondary p-2">
                <!-- PHP Code Insertion -->
                <!-- Вывод 10 записей согласно id страницы. Записи выводятся в обратной сортировке - сначала более новые -->
                <?php for($i=0; $i<count($currentPageTOC); $i++): ?>
                    <a href="postController.php?edit=<?= $currentPageTOC[$i][0] ?>" class="<?= (intval($currentPageTOC[$i][1])===0) ? 'text-danger' : 'text-success' ?>"><?= $status=(intval($currentPageTOC[$i][1])===0) ? '[DRFT]' : '[PUBL]' ?><?= '['.$currentPageTOC[$i][0].'] '.$currentPageTOC[$i][2] ?></a><br>
                <?php endfor; ?>
                <!------------------------>
            </div>
        </div>
        <div class="col-md-4">

        </div>
    </div>
    <div class="row">
        <div class="col-md-12">

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
