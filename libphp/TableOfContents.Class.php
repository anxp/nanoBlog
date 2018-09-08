<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 9/4/18
 * Time: 9:45 AM
 */
class TableOfContents {
    private $itemsPerPage = 10; //default value is 10 records per page, but we can change it in changeNumItemsPerPage method
    private $totalPagesNumber = 0;
    private $totalRecordsNumber; //number of records/articles in DB
    private $db_conn;

    public function getAllItems() { //Get ALL records from DB

    }

    public function getCurrentPageItems($pageNo, int $catID = 0, $associative=false) { //Get list of items for specified page. Pages numeration starts from 1
        if ($pageNo == 0 || $pageNo == null) { //because we will put $_GET['pageNo'] here so it need be sure that PageNo = 1 if this parameter missing or is 0
            $pageNo = 1;
        } else {
            $pageNo = intval($pageNo); //again, $pageNo is $_GET parameter, so it can be anything here, so it's better to filter this value
        }

        $offset = ($pageNo-1)*$this->itemsPerPage;

        //$catID is optional parameter. If catID=0, this method gets records from whole table, paginate them and returns by pages
        //if catID!=0 it returns records only from specified Category

        if($catID === 0) {
            $sql = "SELECT COUNT(*) FROM articles;";
        } elseif ($catID > 0) {
            $sql = "SELECT COUNT(*) FROM articles WHERE category = {$catID};";
        } else {return false;}

        $totalRecordsDirty = $this->db_conn->query($sql); //We'll got not just number of records, but 2-levels nested array in SQL response, so to get just number of records we need additional cleaning
        $totalRecordsClean = intval(reset($totalRecordsDirty[0])); //and now, in $totalRecordsClean is CLEANED INTEGER VALUE == number of records in DB
        $this->totalRecordsNumber = $totalRecordsClean;
        $this->totalPagesNumber = ceil($this->totalRecordsNumber/$this->itemsPerPage); //How many pages will be, based on total amount of records and number of records per page

        if($catID === 0) {
            $sql = "SELECT `art_ID`, `is_published`, `title` FROM `articles` ORDER BY `art_ID` DESC LIMIT {$offset}, {$this->itemsPerPage};";
        } else {
            $sql = "SELECT `art_ID`, `is_published`, `title` FROM `articles` WHERE `category` = {$catID} ORDER BY `art_ID` DESC LIMIT {$offset}, {$this->itemsPerPage};";
        }

        $sqlResponse = $this->db_conn->query($sql); //$sqlResponse contains 2-dim array with: [articleID, articleStatus (published or not) and articleTitle] X10

        if(!$associative) { //USUALLY WE WANT indexed array as response from DB instead of associative
            foreach ($sqlResponse as &$item) {
                $item = array_values($item); //if we don't want associative array, lets reset in to indexed
            }
        }

        return $sqlResponse;
    }

    public function getCategories() {
        $sql = "SELECT `cat_ID`, `cat_name` FROM `categories` WHERE 1;"; //this method hardcoded to get only categories from categories table
        $sqlResponse = $this->db_conn->query($sql); //perform request to MySQL DB
        $responseBody = array();
        //SQL response is 2 or 3-levels nested array, so to get end values we need to dig in...
        for ($i = 0; $i < count($sqlResponse); $i++) {
            $responseBody[intval($sqlResponse[$i]['cat_ID'])] = $sqlResponse[$i]['cat_name']; //REALLY BLACK MAGIC...
        }
        return $responseBody; //return 1-dimensional array with categories such as 'sport', 'politics' etc...

        //At the return we will have array like this:
        /*
        array(4) {
            [4] =>
            string(9) "Cпорт"
            [1] =>
            string(16) "Политика"
            [3] =>
            string(20) "Технологии"
            [2] =>
            string(18) "Экономика"
        }
        */
    }

    public function getCategoryNameByID(int $catID) {
        $sql = "SELECT `cat_name` FROM `categories` WHERE `cat_ID` = {$catID};";
        $sqlResponse = $this->db_conn->query($sql); //perform request to MySQL DB
        $catName = $sqlResponse[0]['cat_name'];
        return $catName;
    }

    //Method returns latest N PUBLISHED records of specified category, N by default = 5
    public function getLatestRecordsByCategory(int $catID, $numberOfRec = 5) {
        $sql = "SELECT `art_ID`, `title` FROM `articles` WHERE `category` = {$catID} AND `is_published` = 1 ORDER BY `art_ID` DESC LIMIT {$numberOfRec};";
        $sqlResponse = $this->db_conn->query($sql); //perform request to MySQL DB
        $responseBody = array();

        for ($i=0; $i<count($sqlResponse); $i++) {
            $responseBody[intval($sqlResponse[$i]['art_ID'])] = $sqlResponse[$i]['title'];
        }

        return (!empty($responseBody) ? $responseBody : false);
    }

    public function getAllKeywordsAsJSON() {
        $sql = "SELECT `kwords` FROM `articles` WHERE `kwords` <> '';";
        $sqlResponse = $this->db_conn->query($sql); //perform request to MySQL DB
        $allKeywordsInOneString = '';

        foreach ($sqlResponse as $value) {
            $allKeywordsInOneString.=$value['kwords'].','; //Gather all fields in one string
        }

        $keywordsArray = explode(',', $allKeywordsInOneString); //Making array from string by [,]

        $keywordsArrayFormatted = [];
        foreach ($keywordsArray as $value) {
            if(!empty(trim($value))) {
                $value = mb_strtolower($value, 'UTF-8'); //Convert all words to lower case
                $keywordsArrayFormatted[] = trim($value); //Let's trim spaces and delete empty values
            }
        }

        $keywordsUniqArr = array_unique($keywordsArrayFormatted);
        $keywordsJSON = json_encode($keywordsUniqArr);
        if($keywordsJSON) {
            return $keywordsJSON;
        } else {return false;}
    }

    public function changeNumItemsPerPage(int $n) { //This is if we want change default value (10)
        $this->itemsPerPage = $n;
    }

    public function getNumItemsPerPage() { //Get $itemsPerPage property
        return ($this->itemsPerPage);
    }

    public function __construct(object $db_conn) {
        $this->db_conn = $db_conn;
    }

    public function getTotalPagesNumber() {
        return ($this->totalPagesNumber);
    }

}