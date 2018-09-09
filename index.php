<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 9/8/18
 * Time: 9:30 AM
 */
//TODO: refactor to Class Autoload
define('DS', DIRECTORY_SEPARATOR);
require_once '.'.DS.'libphp'.DS.'TableOfContents.Class.php';
require_once '.'.DS.'libphp'.DS.'db.class.php';

//TODO: move to separate config file and wrap to try-catch
$db = new DB('essent.mysql.tools', 'essent_db', '2XxMUpHE', 'essent_db');

$TOC = new TableOfContents($db);
$categoriesArr = $TOC->getCategories(); //Load categories from DataBase to indexed array
$keywordsJSON = $TOC->getAllKeywordsAsJSON();
$keywords = json_decode($keywordsJSON);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>nanoBlog: Welcome!</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="./styles/nanoblog-green.css">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">

            <nav class="navbar navbar-expand-md navbar-dark bg-dark mb-2">
                <!-- Navbar content -->
                <a class="navbar-brand" href="http://nanoblog.essent.tools">nanoBlog</a>

                <div class="collapse navbar-collapse" id="navbarText">
                    <ul class="navbar-nav mr-auto">
                        <?php foreach ($categoriesArr as $key => $value) { ?>
                            <li class="nav-item active">
                                <a class="nav-link" href="contentview.php?cat=<?= $key ?>"><?= $value ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>

                <form class="form-inline" method="get" action="contentview.php">
                    <div>
                        <input id="searchField" class="form-control-sm mr-sm-2" autocomplete="off" type="text" placeholder="Не лучше чем Google..." aria-label="Search" name="keywordsearch">
                        <div id="searchDD" class="dropdown-content"></div>
                    </div>
                    <button class="btn btn-outline-success btn-sm my-2 my-sm-0" type="submit">Но вдруг повезет :)</button>

                </form>

            </nav>

        </div>
    </div>
    <div class="row">
        <div class="col-md-2">
        </div>
        <div class="col-md-7">

            <!-- Начало вывода блоков категорий -->
            <?php for ($i=1; $i<=count($categoriesArr); $i++) { ?>
                <div class="border border-success mb-2">
                    <div class="categoryBlockHeader">
                        <p class="h5"><?= $categoriesArr[$i] ?></p>
                    </div>

                    <div class="categoryBlockContent">
                        <?php $recordsList = $TOC->getLatestRecordsByCategory($i)?>

                        <?php foreach ($recordsList as $key => $value) { ?>
                            <a class="text-dark" href="contentview.php?record=<?= $key ?>"><?= $value ?></a><br>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>

        </div>
        <div class="col-md-3">
            <?php include '.'.DS.'templates'.DS.'keywordsblock.tpl.php'; ?>
        </div>
    </div>
</div>
<SURPRISETAG style="display: none" id="hiddenJSON"><?= $keywordsJSON ?></SURPRISETAG>
<script src="./libjs/livetagsearch.js"></script>
</body>
</html>
