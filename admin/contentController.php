<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 9/2/18
 * Time: 9:36 PM
 */
class contentController {
    static public function getCategories(object $db_conn) {
        $sql = "SELECT `cat_name` FROM `categories` WHERE 1;"; //this method hardcoded to get only categories from categories table
        $sqlResponse = $db_conn->query($sql); //perform request to MySQL DB
        $responseBody = array();
        //SQL response is 2-levels nested array, so to get end values we need to dig in...
        foreach ($sqlResponse as $value) {
            foreach ($value as $innerValue) {
                $responseBody[] = $innerValue;
            }
        }
        return $responseBody; //return 1-dimensional array with categories such as 'sport', 'politics' etc...
    }
}