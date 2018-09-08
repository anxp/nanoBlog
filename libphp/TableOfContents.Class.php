<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 9/4/18
 * Time: 9:45 AM
 */
class TableOfContents {
    private $itemsPerPage = 10; //default value is 10 records per page, but we can change it in changeNumItemsPerPage method
    private $totalPagesNumber;
    private $totalRecordsNumber; //number of records/articles in DB
    private $db_conn;

    public function getAllItems() { //Get ALL records from DB

    }

    public function getCurrentPageItems($pageNo, $associative=false) { //Get list of items for specified page. Pages numeration starts from 1
        if ($pageNo == 0 || $pageNo == null) { //because we will put $_GET['pageNo'] here so it need be sure that PageNo = 1 if this parameter missing or is 0
            $pageNo = 1;
        } else {
            $pageNo = intval($pageNo); //again, $pageNo is $_GET parameter, so it can be anything here, so it's better to filter this value
        }

        $offset = ($pageNo-1)*$this->itemsPerPage;

        $sql = "SELECT COUNT(*) FROM articles;";
        $totalRecordsDirty = $this->db_conn->query($sql); //We'll got not just number of records, but 2-levels nested array in SQL response, so to get just number of records we need additional cleaning
        $totalRecordsClean = intval(reset($totalRecordsDirty[0])); //and now, in $totalRecordsClean is CLEANED INTEGER VALUE == number of records in DB
        $this->totalRecordsNumber = $totalRecordsClean;
        $this->totalPagesNumber = ceil($this->totalRecordsNumber/$this->itemsPerPage); //How many pages will be, based on total amount of records and number of records per page
        $sql = "SELECT `art_ID`, `is_published`, `title` FROM `articles` ORDER BY `art_ID` DESC LIMIT {$offset}, {$this->itemsPerPage};";
        $sqlResponse = $this->db_conn->query($sql); //$sqlResponse contains 2-dim array with: [articleID, articleStatus (published or not) and articleTitle] X10

        if(!$associative) { //USUALLY WE WANT indexed array as response from DB instead of associative
            foreach ($sqlResponse as &$item) {
                $item = array_values($item); //if we don't want associative array, lets reset in to indexed
            }
        }

        return $sqlResponse;
    }

    public static function getCategories(object $db_conn) {
        $sql = "SELECT `cat_ID`, `cat_name` FROM `categories` WHERE 1;"; //this method hardcoded to get only categories from categories table
        $sqlResponse = $db_conn->query($sql); //perform request to MySQL DB
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

    public function changeNumItemsPerPage($n) { //This is if we want change default value (10)
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