<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 9/8/18
 * Time: 1:19 PM
 */
define('DS', DIRECTORY_SEPARATOR);
define('IMGPATH', '.'.DS.'img'.DS);

require_once '.'.DS.'libphp'.DS.'TableOfContents.Class.php';
require_once '.'.DS.'libphp'.DS.'Article.Class.php';
require_once '.'.DS.'libphp'.DS.'db.class.php';

$db = new DB('essent.mysql.tools', 'essent_db', '2XxMUpHE', 'essent_db');
$TOC = new TableOfContents($db);
$categoriesArr = $TOC->getCategories(); //Load categories from DataBase to indexed array

switch (true) {
    case(isset($_GET['record']) && is_numeric(intval($_GET['record']))):
        $requestedRecID = intval($_GET['record']);
        if($requestedRecord = Article::readFromDB($db, $requestedRecID)) {
            $recID = $requestedRecord->get_artID();
            $isPub = $requestedRecord->get_isPublished(); //usually, user sees only published records, so this is just for control
            $title = $requestedRecord->get_title();
            $body = $requestedRecord->get_content();
            $catID = $requestedRecord->get_category();
            $kwords = $requestedRecord->get_kwords();
            $attImg = $requestedRecord->get_attImage();
            $catName = $TOC->getCategoryNameByID($catID);

            include '.'.DS.'templates'.DS.'recordview.tpl.php';
            break;
        }

    case(isset($_GET['cat']) && is_numeric(intval($_GET['cat']))):
        $requestedCatID = intval($_GET['cat']);
        $pageNo = isset($_GET['page']) ? intval($_GET['page']) : 0;
        if($itemsInCategory = $TOC->getCurrentPageItems($pageNo, $requestedCatID)) {
            $catName = $TOC->getCategoryNameByID($requestedCatID);
            $totalPagesNum = $TOC->getTotalPagesNumber();

            include '.'.DS.'templates'.DS.'catview.tpl.php';
            break;
        }

    default:
        echo '404 :(';
}

//include 'keywordsview.tpl.php';