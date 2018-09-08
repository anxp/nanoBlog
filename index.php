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

//TODO: move to separate config file
$db = new DB('essent.mysql.tools', 'essent_db', '2XxMUpHE', 'essent_db');

$TOC = new TableOfContents($db);
$categoriesArr = $TOC->getCategories(); //Load categories from DataBase to indexed array
$keywords = json_decode($TOC->getAllKeywordsAsJSON());
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

                <form class="form-inline">
                    <input class="form-control-sm mr-sm-2" type="search" placeholder="Не лучше чем Google..." aria-label="Search">
                    <button class="btn btn-outline-success btn-sm my-2 my-sm-0" type="submit">Но вдруг повезет :)</button>
                </form>
            </nav>

        </div>
    </div>
    <div class="row">
        <div class="col-md-2">
        </div>
        <div class="col-md-7">
            <div class="carousel slide" id="carousel-89849">
                <ol class="carousel-indicators">
                    <li data-slide-to="0" data-target="#carousel-89849">
                    </li>
                    <li data-slide-to="1" data-target="#carousel-89849">
                    </li>
                    <li data-slide-to="2" data-target="#carousel-89849" class="active">
                    </li>
                </ol>
                <div class="carousel-inner">
                    <div class="carousel-item">
                        <img class="d-block w-100" alt="Carousel Bootstrap First" src="https://www.layoutit.com/img/sports-q-c-1600-500-1.jpg">
                        <div class="carousel-caption">
                            <h4>
                                First Thumbnail label
                            </h4>
                            <p>
                                Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.
                            </p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img class="d-block w-100" alt="Carousel Bootstrap Second" src="https://www.layoutit.com/img/sports-q-c-1600-500-2.jpg">
                        <div class="carousel-caption">
                            <h4>
                                Second Thumbnail label
                            </h4>
                            <p>
                                Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.
                            </p>
                        </div>
                    </div>
                    <div class="carousel-item active">
                        <img class="d-block w-100" alt="Carousel Bootstrap Third" src="https://www.layoutit.com/img/sports-q-c-1600-500-3.jpg">
                        <div class="carousel-caption">
                            <h4>
                                Third Thumbnail label
                            </h4>
                            <p>
                                Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.
                            </p>
                        </div>
                    </div>
                </div> <a class="carousel-control-prev" href="#carousel-89849" data-slide="prev"><span class="carousel-control-prev-icon"></span> <span class="sr-only">Previous</span></a> <a class="carousel-control-next" href="#carousel-89849" data-slide="next"><span class="carousel-control-next-icon"></span> <span class="sr-only">Next</span></a>
            </div>

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
            <div class="border border-success mb-2">
                <div class="categoryBlockHeader">
                    <p class="h5">Ключевые слова</p>
                </div>

                <div class="categoryBlockContent">
                    <?php foreach ($keywords as $value) { ?>
                        <span>[</span><?= $value ?><span>] </span>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/scripts.js"></script>
</body>
</html>
