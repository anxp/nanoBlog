<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 9/4/18
 * Time: 7:55 PM
 */
class PostController {
    private $db_conn;

    public function __construct(object $db_conn) {
        $this->db_conn = $db_conn;
    }

    //This method will be used to handle NEW post/article.
    //It responsible for NOT data loss in case of user error, and for give user descriptive error message if something goes wrong
    public function newPostHandler(array $POST) {
        if (empty($POST)) {return;} //We don't even try to handle new record if $POST is empty, so THERE IS NO RECORD!
        if (is_numeric($POST['category']) && !empty($POST['title']) && !empty($POST['body'])) {
            //if category set correct, title and body also not empty, let write article to Database:
            //Order of parameters in constructor are: 1.isPublished 2.title 3.content 4.category 5.kwords 6.attImage
            $article = new Article($POST['status'], $POST['title'], $POST['body'], $POST['category'], '', ''); //Let's create new Article object
            if($article->saveToDB($this->db_conn)) { //Trying to save article to Database
                $_SESSION['answertype'] = 'OK';
                $_SESSION['message'] = 'Record saved to DB!';
                self::resetDraft(); //If all fine, we don't need saved draft anymore
                return; //Return back to calling code

            } else { //If $article->saveToDB method returned FALSE we need to handle the error
                $_SESSION['answertype'] = 'ERROR';
                $_SESSION['message'] = 'Article->saveToDB method returned FALSE';
                return; //Return back to calling code
            }

        } else { //We fall in this section when user has not filled all required fields, such as Title, Category, and Body
            //return error with description what's wrong, and saved user input!!!, so it's not necessary to retype all content from scratch:
            switch (true) {
                case (!is_numeric($POST['category'])):
                    $_SESSION['answertype'] = 'ERROR';
                    $_SESSION['message'] = 'Wrong category.';
                    $_SESSION['saveduserinput'] = json_encode($POST);
                    break;
                case (empty($POST['title'])):
                    $_SESSION['answertype'] = 'ERROR';
                    $_SESSION['message'] = 'Empty title.';
                    $_SESSION['saveduserinput'] = json_encode($POST);
                    break;
                case (empty($POST['body'])):
                    $_SESSION['answertype'] = 'ERROR';
                    $_SESSION['message'] = 'Empty body.';
                    $_SESSION['saveduserinput'] = json_encode($POST);
                    break;
                default:
                    echo 'Something really went wrong...';
            }
            return; //Return back to calling code
        }
    }

    public function routineHandler(array $GET) {
        switch (true) {
            //If user explicitly ordered to DELETE draft of article/record
            case (isset($GET['resetall']) && $GET['resetall'] === 'true'):
                self::resetDraft();
                break;
            case (isset($GET['edit'])):
                break;
        }
        return; //Return back to calling code
    }

    public static function resetDraft() {
        unset($_SESSION['answertype']);
        unset($_SESSION['message']);
        unset($_SESSION['saveduserinput']);
    }
}