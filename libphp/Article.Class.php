<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 9/3/18
 * Time: 11:22 AM
 */
//TODO - check Active Record pattern and maybe rewrite this class (Article) according to it

class Article {
    //Default value of artID is null. Why? Because if user forget specify it, and trying to update something in DB, we will allow
    //DB-updating only when artID is numeric value,
    //also, we will not set article ID in constructor because this field automatically autoincremented in Database
    private $artID = null;
    private $isPublished; //0 == not published, 1 == published
    private $title; //Title of article
    private $content;  //Body of article
    private $category; //Category of article INT!!!! not STRING!!!
    private $kwords; //String with keywords separated with [,]
    private $attImage; //Attached image to article

    private function __construct(){}

    //This "constructor" we will use when new article creating:
    public static function newArticle($isPublished, $title, $content, $category, $kwords, $attImage)
    {
        $newArticle = new self;
        $newArticle->isPublished = intval($isPublished);
        $newArticle->title = self::cleanString($title);
        $newArticle->content = trim($content); //the only variable we can't filter, because html is allowed in our text form
        $newArticle->category = intval($category);
        $newArticle->kwords = self::cleanString($kwords);
        $newArticle->attImage = self::cleanString($attImage);
        return $newArticle;
    }

    //This "constructor" will be used when working with ALREADY EXISTING article:
    public static function existingArticle($artID, $isPublished, $title, $content, $category, $kwords, $attImage = '')
    {
        $existingArticle = new self;
        $existingArticle->artID = intval($artID);
        $existingArticle->isPublished = intval($isPublished);
        $existingArticle->title = self::cleanString($title);
        $existingArticle->content = trim($content); //the only variable we can't filter, because html is allowed in our text form
        $existingArticle->category = intval($category);
        $existingArticle->kwords = self::cleanString($kwords);
        $existingArticle->attImage = self::cleanString($attImage);
        return $existingArticle;
    }

    public function saveToDB(object $db_conn)
    {
        $isPublished = $db_conn->escape($this->isPublished);
        $title = $db_conn->escape($this->title);
        $content = $db_conn->escape($this->content);
        $category = $db_conn->escape($this->category);
        $kwords = $db_conn->escape($this->kwords);
        $attImage = $db_conn->escape($this->attImage);

        $sql = "INSERT INTO `articles` (`is_published`, `title`, `content`, `category`, `kwords`, `att_image`) VALUES ('{$isPublished}', '{$title}', '{$content}', '{$category}', '{$kwords}', '{$attImage}');";
        $sqlResponse = $db_conn->query($sql);

        return ($sqlResponse); //usually true or false
    }

    public function updateToDB(object $db_conn)
    {
        if (!$this->artID) {return false;} //If user have not specified artID, we don't even try to update something in DB!
        $artID = $this->artID;
        $isPublished = $db_conn->escape($this->isPublished);
        $title = $db_conn->escape($this->title);
        $content = $db_conn->escape($this->content);
        $category = $db_conn->escape($this->category);
        $kwords = $db_conn->escape($this->kwords);
        $attImage = $db_conn->escape($this->attImage);

        if(empty($attImage)) { //If attImage was not specified, we will not even touch it in DB
            $sql = "UPDATE `articles` SET `is_published` = '{$isPublished}', `title` = '{$title}', `content` = '{$content}', `category` = '{$category}', `kwords` = '{$kwords}' WHERE `art_ID` = '{$artID}' LIMIT 1;";
        } else {
            //Otherwise - rewrite att_image field with new value:
            $sql = "UPDATE `articles` SET `is_published` = '{$isPublished}', `title` = '{$title}', `content` = '{$content}', `category` = '{$category}', `kwords` = '{$kwords}', `att_image` = '{$attImage}' WHERE `art_ID` = '{$artID}' LIMIT 1;";
        }

        $sqlResponse = $db_conn->query($sql);

        return ($sqlResponse); //usually true or false
    }

    //This method is static because we want to call it at the moment when object still not exists;
    //If read from DB will OK, this method will return us a new Article object
    public static function readFromDB(object $db_conn, $artID)
    {
        $sql = "SELECT `art_ID`, `is_published`, `title`, `content`, `category`, `kwords`, `att_image` FROM `articles` WHERE `art_ID` = {$artID};";
        $sqlResponse = $db_conn->query($sql);
        if(isset($sqlResponse[0])) {
            $id = intval($sqlResponse[0]['art_ID']);
            $isPublished = intval($sqlResponse[0]['is_published']);
            $title = $sqlResponse[0]['title'];
            $body = $sqlResponse[0]['content'];
            $cat = intval($sqlResponse[0]['category']);
            $kwords = $sqlResponse[0]['kwords'];
            $img = $sqlResponse[0]['att_image'];

            $newObject = self::existingArticle($id, $isPublished, $title, $body, $cat, $kwords, $img);

            return ($newObject); //if SQL response was not empty, we've created new Article Object and return it to user
        } else {
            return false; //Otherwise method returns false.
        }
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

    public static function cleanString($str) {
        $str = trim($str);
        $str = strip_tags($str);
        return $str;
    }

    /**
     * @return mixed
     */
    public function get_artID(): int
    {
        return $this->artID;
    }

    /**
     * @return int
     */
    public function get_isPublished(): int
    {
        return $this->isPublished;
    }

    /**
     * @return string
     */
    public function get_title(): string
    {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function get_content(): string
    {
        return $this->content;
    }

    /**
     * @return int
     */
    public function get_category(): int
    {
        return $this->category;
    }

    /**
     * @return string
     */
    public function get_kwords(): string
    {
        return $this->kwords;
    }

    /**
     * @return string
     */
    public function get_attImage(): string
    {
        return $this->attImage;
    }
}