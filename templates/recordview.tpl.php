<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>nanoBlog: <?= $title ?></title>

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
                            <li class="nav-item <?= ($key == $catID) ? 'active' : '' ?>">
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
            <!-- Вывод блока записи -->

                <div class="border border-success mb-2">
                    <div class="categoryBlockHeader">
                        <p class="h4"><?= $title ?></p>
                    </div>

                    <div style="display: block" class="m-2">
                        <?php if ($attImg) { ?>
                        <img class="leadimage" src="<?= IMGPATH.$attImg ?>">
                        <?php } ?>
                    </div>

                    <div class="categoryBlockContent">
                        <?= $body ?>
                    </div>

                    <div class="categoryBlockFooter">
                        <p><span class="text-warning">Ключевые слова для этой записи:</span> <?= $kwords ?></p>
                    </div>
                </div>

        </div>
        <div class="col-md-3">
        </div>
    </div>
</div>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/scripts.js"></script>
</body>
</html>
