<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 9/4/18
 * Time: 9:45 AM
 */
class TableOfContents {
    private $itemsPerPage = 10; //default value is 10 records per page, but we can change it in changeNumItemsPerPage method
    private $pagesNumber = 0; //number of pages for pagination based on all DB records OR (if requested) per-category
    private $recordsNumber; //number of records/articles in whole DB OR per-category
    private $db_conn;

    //This method returns records for Admin Panel - PUBLUSHED AND DRAFTS! Records can also be returned by-category, if specified
    public function getRecordsForAdminPanel($pageNo, int $catID = 0, $associative=false) {
        if (intval($pageNo) === 0) { //because we will put $_GET['pageNo'] here so it need be sure that PageNo = 1 if this parameter missing or is 0
            $pageNo = 1;
        } else {
            $pageNo = intval($pageNo); //again, $pageNo is $_GET parameter, so it can be anything here, so it's better to filter this value
        }

        $offset = ($pageNo-1)*$this->itemsPerPage; //offset calculation for pagination

        //----- Prepared statements for Admin Panel to get:
        // 1) Number of ALL records in DB; 2) Number of records in category;
        // 3) Titles of ALL but for specified Page; 4) Titles of by_CATEGORY but for specified Page.
        $queryNumOfRec_ALL = "SELECT COUNT(*) FROM `articles`;";
        $queryNumOfRec_byCAT = "SELECT COUNT(*) FROM articles WHERE category = ?;";
        $queryRecords_byPAGE = "SELECT `art_ID`, `is_published`, `title` FROM `articles` ORDER BY `art_ID` DESC LIMIT ?, ?;";
        $queryRecords_byPAGEbyCAT = "SELECT `art_ID`, `is_published`, `title` FROM `articles` WHERE `category` = ? ORDER BY `art_ID` DESC LIMIT ?, ?;";

        //$catID is optional parameter. If catID=0, this method gets records from whole table, paginate them and returns by pages
        //if catID!=0 it returns records only from specified Category
        if($catID === 0) {
            $numOfRecords = $this->db_conn->run($queryNumOfRec_ALL)->fetchColumn();
            $stmt = $this->db_conn->run($queryRecords_byPAGE, [$offset, $this->itemsPerPage]);
        } elseif($catID > 0) {
            $numOfRecords = $this->db_conn->run($queryNumOfRec_byCAT, [$catID])->fetchColumn();
            $stmt = $this->db_conn->run($queryRecords_byPAGEbyCAT, [$catID, $offset, $this->itemsPerPage]);
        } else {return false;}


        $this->recordsNumber = $numOfRecords;
        $this->pagesNumber = ceil($this->recordsNumber/$this->itemsPerPage); //How many pages will be, based on total amount of records (whole OR per-category, if requested) and number of records per page

        return $stmt; //We return PDOStatement Object
    }

    //This method returns ONLY PUBLISHED records - for display on public-access pages
    public function getRecordsForWorld($pageNo, int $catID = 0, $kWord = null) { //Get list of items for specified page, category, and keyword. Pages numeration starts from 1
        if (intval($pageNo) === 0) { //because we will put $_GET['pageNo'] here so it need be sure that PageNo = 1 if this parameter missing or is 0
            $pageNo = 1;
        } else {
            $pageNo = intval($pageNo); //again, $pageNo is $_GET parameter, so it can be anything here, so it's better to filter this value
        }

        $offset = ($pageNo-1)*$this->itemsPerPage;

        if($kWord) {$kWord = "%{$kWord}%";} //for LIKE search in DB

        //----- Prepared statements for Number Of Records with different conditions:
        $queryNumOfPublished_ALL = "SELECT COUNT(*) FROM `articles` WHERE `is_published` = 1;";
        $queryNumOfPublished_wKEYWORD = "SELECT COUNT(*) FROM `articles` WHERE `is_published` = 1 AND `kwords` LIKE ?;";
        $queryNumOfPublished_byCAT = "SELECT COUNT(*) FROM `articles` WHERE `is_published` = 1 AND `category` = ?;";
        $queryNumOfPublished_byCATwKEYWORD = "SELECT COUNT(*) FROM `articles` WHERE `is_published` = 1 AND `category` = ? AND `kwords` LIKE ?;";

        //----- Prepared statements for Get Titles of Articles with different conditions:
        $queryRecords_byPAGE = "SELECT `art_ID`, `title` FROM `articles` WHERE `is_published` = 1 ORDER BY `art_ID` DESC LIMIT ?, ?;";
        $queryRecords_byPAGEwKEYWORD = "SELECT `art_ID`, `title` FROM `articles` WHERE `is_published` = 1 AND `kwords` LIKE ? ORDER BY `art_ID` DESC LIMIT ?, ?;";
        $queryRecords_byPAGEbyCAT = "SELECT `art_ID`, `title` FROM `articles` WHERE `is_published` = 1 AND `category` = ? ORDER BY `art_ID` DESC LIMIT ?, ?;";
        $queryRecords_byPAGEbyCATwKEYWORD = "SELECT `art_ID`, `title` FROM `articles` WHERE `is_published` = 1 AND `category` = ? AND `kwords` LIKE ? ORDER BY `art_ID` DESC LIMIT ?, ?;";


        switch(true) {
            case($catID === 0 && $kWord === null):
                $numOfRecords = $this->db_conn->run($queryNumOfPublished_ALL)->fetchColumn();
                $stmt = $this->db_conn->run($queryRecords_byPAGE, [$offset, $this->itemsPerPage]);
                break;

            case($catID === 0 && $kWord !== null):
                $numOfRecords = $this->db_conn->run($queryNumOfPublished_wKEYWORD, [$kWord])->fetchColumn();
                $stmt = $this->db_conn->run($queryRecords_byPAGEwKEYWORD, [$kWord, $offset, $this->itemsPerPage]);
                break;

            case($catID > 0 && $kWord === null):
                $numOfRecords = $this->db_conn->run($queryNumOfPublished_byCAT, [$catID])->fetchColumn();
                $stmt = $this->db_conn->run($queryRecords_byPAGEbyCAT, [$catID, $offset, $this->itemsPerPage]);
                break;

            case($catID > 0 && $kWord !== null):
                $numOfRecords = $this->db_conn->run($queryNumOfPublished_byCATwKEYWORD, [$catID, $kWord])->fetchColumn();
                $stmt = $this->db_conn->run($queryRecords_byPAGEbyCATwKEYWORD, [$catID, $kWord, $offset, $this->itemsPerPage]);
                break;

            default:
                return false;
        }

        $this->recordsNumber = $numOfRecords;
        $this->pagesNumber = ceil($this->recordsNumber/$this->itemsPerPage); //How many pages will be, based on total amount of records and number of records per page

        return $stmt;
    }

    public function getCategories() {
        $queryGetCategories = "SELECT `cat_ID`, `cat_name` FROM `categories` WHERE 1;";
        $stmt = $this->db_conn->run($queryGetCategories); //This is object of class PDOStatement, we will do method "fetch" to extract data from it

        $assocArray = array();

        while ($row = $stmt->fetch(PDO::FETCH_LAZY)) {
            $assocArray[$row['cat_ID']] = $row['cat_name'];
        }

        return $assocArray;
    }

    public function getCategoryNameByID(int $catID) {
        $queryGetCategoryName = "SELECT `cat_name` FROM `categories` WHERE `cat_ID` = ?;";
        $catName = $this->db_conn->run($queryGetCategoryName, [$catID])->fetchColumn();
        return $catName;
    }

    public function getAllKeywordsAsJSON() {
        $queryGetAllKeywords = "SELECT `kwords` FROM `articles` WHERE `kwords` <> '';";
        $stmt = $this->db_conn->run($queryGetAllKeywords);

        $allKeywordsInOneString = '';

        while ($row = $stmt->fetch(PDO::FETCH_LAZY)) {
            $allKeywordsInOneString.=$row['kwords'].','; //Gather all fields in one string
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
        $keywordsUniqArrIndexed = array_values($keywordsUniqArr);
        $keywordsJSON = json_encode($keywordsUniqArrIndexed);
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
        return ($this->pagesNumber);
    }

}