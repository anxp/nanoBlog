<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 9/3/18
 * Time: 11:22 AM
 */
class Article {
    private $artID; //we will not set article ID in constructor because this field automatically autoincremented in Database
    private $isPublished; //Boolean status == 0 || 1 (published or not)
    private $title; //Title of article
    private $content;  //Body of article
    private $category; //Category of article INT!!!! not STRING!!!
    private $kwords; //String with keywords separated with [,]

    public function __construct($isPublished, $title, $content, $category, $kwords)
    {
        $this->isPublished = $isPublished;
        $this->title = $title;
        $this->content = $content;
        $this->category = $category;
        $this->kwords = $kwords;
    }

    public function saveToDB(object $db_conn)
    {
        $isPublished = $db_conn->escape($this->isPublished);
        $title = $db_conn->escape($this->title);
        $content = $db_conn->escape($this->content);
        $category = $db_conn->escape($this->category);
        $kwords = $db_conn->escape($this->kwords);

        $sql = "INSERT INTO `articles` (`is_published`, `title`, `content`, `category`, `kwords`) VALUES ('{$isPublished}', '{$title}', '{$content}', '{$category}', '$kwords');";
        $sqlResponse = $db_conn->query($sql);

        return ($sqlResponse);
    }

    public static function getCategories(object $db_conn) {
        $sql = "SELECT `cat_ID`, `cat_name` FROM `categories` WHERE 1;"; //this method hardcoded to get only categories from categories table
        $sqlResponse = $db_conn->query($sql); //perform request to MySQL DB
        $responseBody = array();
        //SQL response is 2 or 3-levels nested array, so to get end values we need to dig in...
        for ($i=0; $i<count($sqlResponse); $i++) {
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
}