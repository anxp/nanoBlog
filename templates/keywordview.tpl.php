<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>nanoBlog: <?= $requestedKeyword ?></title>

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
                        <input id="searchField" class="form-control-sm mr-sm-2" type="search" placeholder="Не лучше чем Google..." aria-label="Search" name="keywordsearch">
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

            <!-- Вывод оглавления категории (список записей в этой категории) -->
            <div class="border border-success mb-2">
                <div class="categoryBlockHeader">
                    <p class="h5">Результат поиска по ключевому слову: "<?= $requestedKeyword ?>":</p>
                </div>

                <div class="categoryBlockContent">
                    <?php for ($i=0; $i<count($itemsByKeyword); $i++) { ?>
                        <a class="text-dark" href="contentview.php?record=<?= $itemsByKeyword[$i][0] ?>"><?= $itemsByKeyword[$i][2] ?></a><br>
                    <?php } ?>
                </div>
            </div>

            <!-- Вывод блока пагинации -->
            <div class="d-flex justify-content-center">
                <nav>
                    <ul class="pagination">
                        <!-- Вывод начального блока пагинации (кнопки Предыдущая и Первая) -->
                        <li class="page-item">
                            <a class="page-link text-success" href="contentview.php?page=1&keywordsearch=<?= isset($_GET['keywordsearch']) ? $_GET['keywordsearch'] : null ?>">Первая</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link text-success" href="contentview.php?page=<?= (intval(isset($_GET['page']) ? $_GET['page'] : 0)-1) < 0 ? 0 : (intval(isset($_GET['page']) ? $_GET['page'] : 0)-1) ?>&keywordsearch=<?= isset($_GET['keywordsearch']) ? $_GET['keywordsearch'] : null ?>">Предыдущая</a>
                        </li>
                        <!------------------------------------------------------------------->

                        <!-- Вывод центрального блока пагинации (цифры) -->
                        <?php for($i=1; $i<=$totalPagesNum; $i++): ?>
                            <li class="page-item">
                                <a class="page-link text-success" href="contentview.php?page=<?= $i ?>&keywordsearch=<?= isset($_GET['keywordsearch']) ? $_GET['keywordsearch'] : null ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <!------------------------------------------------>

                        <!-- Вывод конечного блока пагинации (кнопки Следующая и Последняя) -->
                        <li class="page-item">
                            <a class="page-link text-success" href="contentview.php?page=<?= (intval(isset($_GET['page']) ? $_GET['page'] : 0)+1) <= $totalPagesNum ? (intval(isset($_GET['page']) ? $_GET['page'] : 0)+1) : $totalPagesNum ?>&keywordsearch=<?= isset($_GET['keywordsearch']) ? $_GET['keywordsearch'] : null ?>">Следующая</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link text-success" href="contentview.php?page=<?= $totalPagesNum ?>&keywordsearch=<?= isset($_GET['keywordsearch']) ? $_GET['keywordsearch'] : null ?>">Последняя</a>
                        </li>
                        <!-------------------------------------------------------------------->
                    </ul>
                </nav>
            </div>

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
