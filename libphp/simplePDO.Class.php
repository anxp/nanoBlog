<?php

class simplePDO extends PDO {
    private $opt = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //because we want fetch returns only assoc array (not indexed)
        PDO::ATTR_EMULATE_PREPARES => FALSE, //To eliminate this SQL bug/feature: https://stackoverflow.com/questions/10014147/limit-keyword-on-mysql-with-prepared-statement
    );

    public function __construct($host, $user, $password, $db_name) {
        $dsn = "mysql:host=$host;dbname=$db_name;charset=UTF8";
        //$this->connection = new PDO($dsn, $user, $password, $this->opt);
        parent::__construct($dsn, $user, $password, $this->opt);
    }

    //This function is for simplification - it combines Prepare & Execute
    public function run($sql, array $args = NULL) {
        $stmt = $this->prepare($sql);
        $stmt->execute($args);
        return $stmt;
    }

    //For the future:
    /*
    public function insert($statement) {
        $this->beginTransaction();
        $status = $this->exec($statement);
        if ($status) {
            $this->commit();
        } else {
            $this->rollback();
        }
    }*/
}

/*
 * Usage:
 * $db = new simplePDO('mysql.host.com', 'username', 'password', 'name_of_db');
 * $preparedQuery = "SELECT `art_ID`, `title` FROM `articles` WHERE `is_published` = 1 AND `category` = ? AND `kwords` LIKE ?;";
 * $stmt = $db->run($preparedQuery, [1, '%украина%']);
 *
 * Get all in one array:
 * $data = $stmt->fetchAll();
 *
 * Or get by row:
 * while ($row = $stmt->fetch(PDO::FETCH_LAZY))
 * {
 * echo $row['art_ID'];
 * echo $row['title'].PHP_EOL;
 * }
 *
 * Where art_ID and title - names of columns in DB table
 */