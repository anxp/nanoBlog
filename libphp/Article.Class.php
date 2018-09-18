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
        $queryInsertToDB = "INSERT INTO `articles` (`is_published`, `title`, `content`, `category`, `kwords`, `att_image`) VALUES (?, ?, ?, ?, ?, ?);";
        //TODO: add try-catch wrapper to this operation:
        $stmt = $db_conn->run($queryInsertToDB, [$this->isPublished, $this->title, $this->content, $this->category, $this->kwords, $this->attImage]);

        if($stmt instanceof PDOStatement)
            return true;
        else
            return false;
    }

    public function updateToDB(object $db_conn)
    {
        if (!$this->artID) {return false;} //If user have not specified artID, we don't even try to update something in DB!

        if(empty($this->attImage)) { //If attImage was not specified, we will not even touch it in DB
            $queryUpdateToDB = "UPDATE `articles` SET `is_published` = ?, `title` = ?, `content` = ?, `category` = ?, `kwords` = ? WHERE `art_ID` = ? LIMIT 1;";
            //TODO: wrap in try-catch
            $stmt = $db_conn->run($queryUpdateToDB, [$this->isPublished, $this->title, $this->content, $this->category, $this->kwords, $this->artID]);
        } else {
            //Otherwise - rewrite att_image field with new value:
            $queryUpdateToDB = "UPDATE `articles` SET `is_published` = ?, `title` = ?, `content` = ?, `category` = ?, `kwords` = ?, `att_image` = ? WHERE `art_ID` = ? LIMIT 1;";
            //TODO: wrap in try-catch
            $stmt = $db_conn->run($queryUpdateToDB, [$this->isPublished, $this->title, $this->content, $this->category, $this->kwords, $this->attImage, $this->artID]);
        }

        if($stmt instanceof PDOStatement)
            return true;
        else
            return false;
    }

    //This method is static because we want to call it at the moment when object still not exists;
    //If read from DB will OK, this method will return us a new Article object
    public static function readFromDB(object $db_conn, $artID)
    {
        $queryReadArticle = "SELECT `art_ID`, `is_published`, `title`, `content`, `category`, `kwords`, `att_image` FROM `articles` WHERE `art_ID` = ?;";
        $stmt = $db_conn->run($queryReadArticle, [$artID]);
        $sqlResponse = $stmt->fetchAll();
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