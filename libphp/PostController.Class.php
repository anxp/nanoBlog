<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 9/4/18
 * Time: 7:55 PM
 */

//This class implements Data Mapper pattern - intermediary between User Data and Article Object
class PostController {
    private $db_conn;
    private $uploadedImageName;

    const SUPPORTED_FILES = ['image/jpeg', 'image/png', 'image/gif']; //Maybe make regular field from this, so user can re-assign it...?
    const IMG_DIR = '..'.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR;
    const THUMB_DIR = '..'.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.'thumb'.DIRECTORY_SEPARATOR;

    public function __construct(object $db_conn) {
        $this->db_conn = $db_conn;
    }

    //This method will be used to handle EXISTING & EDITED post/article.
    public function existingEditedPostHandler(array $POST, array $FILES) {

        //We don't even try to handle 'as existing record' if $POST is empty (no record at all), so if $POST is empty -> return
        if (empty($POST)) {return;}

        //We proceed ONLY if isset $POST['artid'], - this is a sign of we deal with EDITED RECORD, not new!
        if (isset($POST['artid']) && intval($POST['artid']) > 0) {

            $artId = intval($POST['artid']);
            $attImage = ($this->uploadedFilesHandler($FILES)) ? ($this->uploadedImageName) : ''; //Name of uploaded image, if upload-OK or upload-NotOK
            //$attImage can be: 'somename' or ''. Method updateToDB will rewrite field in DB if 'somename' or just untouch it if '' - so we'll not lost image in DB if already exists
            $editedArticle = Article::existingArticle($artId, $POST['status'], $POST['title'], $POST['body'], $POST['category'], '', $attImage);

            //Now we can update article/record in DB
            if ($editedArticle->updateToDB($this->db_conn)) { //Trying to save EDITED article to Database
                $_SESSION['answertype'] = 'SUCCESS';
                $_SESSION['message'] = 'Record/article updated to DB.';
                return; //Return back to calling code
            } else { //If $article->updateToDB method returned FALSE we need to handle the error
                $_SESSION['answertype'] = 'ERROR';
                $_SESSION['message'] = 'Article->updateToDB method returned FALSE';
                return; //Return back to calling code
            }

        } else {
            return;
        }
    }

    //This method will be used to handle NEW post/article.
    //It responsible for NOT data loss in case of user error, and for give user descriptive error message if something goes wrong
    public function newPostHandler(array $POST, array $FILES) {

        //We don't even try to handle new record if $POST is empty (no new record),
        //OR POST[] contains element 'artid' (article ID), which means this is NOT A NEW RECORD, but edited existing!
        if (empty($POST) || isset($POST['artid'])) {return;}

        if (is_numeric($POST['category']) && !empty($POST['title']) && !empty($POST['body'])) {
            //if category set correct, title and body also not empty, let write article to Database:
            //Order of parameters in constructor are: 1.isPublished 2.title 3.content 4.category 5.kwords 6.attImage

            $attImage = ($this->uploadedFilesHandler($FILES)) ? ($this->uploadedImageName) : ''; //Name of uploaded image, if exists

            $article = Article::newArticle($POST['status'], $POST['title'], $POST['body'], $POST['category'], '', $attImage); //Let's create new Article object

            //Trying to save article to Database
            if($article->saveToDB($this->db_conn)) {
                $_SESSION['answertype'] = 'SUCCESS';
                $_SESSION['message'] = 'Record/article saved to DB.';
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
                    $_SESSION['content'] = json_encode($POST);
                    break;
                case (empty($POST['title'])):
                    $_SESSION['answertype'] = 'ERROR';
                    $_SESSION['message'] = 'Empty title.';
                    $_SESSION['content'] = json_encode($POST);
                    break;
                case (empty($POST['body'])):
                    $_SESSION['answertype'] = 'ERROR';
                    $_SESSION['message'] = 'Empty body.';
                    $_SESSION['content'] = json_encode($POST);
                    break;
                default:
                    echo 'Something really went wrong...';
            }
            return; //Return back to calling code
        }
    }

    //This method will be used for routine actions - delete draft, or load article from DB if user requested it
    public function routineHandler(array $GET) {
        switch (true) {
            //If user explicitly ordered to DELETE draft of article/record
            case (isset($GET['resetall']) && $GET['resetall'] === 'true'):
                self::resetDraft();
                break;
            //If user wants to edit existing article/record
            case (isset($GET['edit'])):
                if($articleToEdit = Article::readFromDB($this->db_conn, intval($GET['edit']))) {

                    $articleToEditAssocArray = array(); //This array we'll include in server reply to user. All fields of requested record will be here.
                    $articleToEditAssocArray['artid'] = $articleToEdit->get_artID();
                    $articleToEditAssocArray['status'] = $articleToEdit->get_isPublished();
                    $articleToEditAssocArray['title'] = $articleToEdit->get_title();
                    $articleToEditAssocArray['body'] = $articleToEdit->get_content();
                    $articleToEditAssocArray['category'] = $articleToEdit->get_category();
                    $articleToEditAssocArray['keywords'] = $articleToEdit->get_kwords();
                    $articleToEditAssocArray['attimage'] = $articleToEdit->get_attImage();

                    $_SESSION['answertype'] = 'SUCCESS';
                    $_SESSION['message'] = 'Record/article loaded.';
                    $_SESSION['content'] = json_encode($articleToEditAssocArray); //Pack whole record/article to JSON to more easily send to frontend
                }
                break;
        }
        return; //Return back to calling code
    }

    //This method used to handle uploaded files (images) - check them, rename, move from temporary storage to website folder
    //This method private, because we don't want allow call it outside newArticle / existingArticle context - image without article makes no sense
    private function uploadedFilesHandler(array $FILES) :bool {

        if (empty($FILES) || !isset($FILES['artimage'])) {return false;} //If we got not what we exactly expect, just return
        if ($FILES['artimage']['error'] !== UPLOAD_ERR_OK) {return false;} //TODO: this case is really need to be handled, and message to user must be sent

        //Check if file type is IN ALLOWED TYPES array. We don't trust $FILES['artimage']['type'] and check real MIME Content Type:
        if (!in_array(mime_content_type($FILES['artimage']['tmp_name']), self::SUPPORTED_FILES)) {return false;}

        $origName = basename($FILES['artimage']['name']); //Get original file name
        $fileExt = pathinfo($origName, PATHINFO_EXTENSION); //Get file extension
        $newName = uniqid().'.'.$fileExt; //Create new uniq name. TODO: improve to be more uniq
        /*if (file_exists(self::IMG_DIR.$newName)) {
            //TODO: do something if file w/ same name exists. More uniq?
            //At the moment just ignoring this, overwrite
            }*/
        $moveResult = move_uploaded_file($FILES["artimage"]["tmp_name"],self::IMG_DIR.$newName);

        if ($moveResult) {
            Image::generateThumbnail(self::IMG_DIR.$newName, self::THUMB_DIR, 250);
            $this->uploadedImageName = $newName; //Save file name in object property, so methods newArticle and existingArticle can access it
            return $moveResult;
        } else {
            return false;
        }
    }

    public static function resetDraft() {
        unset($_SESSION['answertype']);
        unset($_SESSION['message']);
        unset($_SESSION['content']);
    }
}