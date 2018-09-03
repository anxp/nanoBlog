<?php
//------- This file included in EVERY ADMIN-PANEL SCRIPT to ensure that user role == admin (or else if set in this file)
ini_set('session.save_path', '/Users/andrey/Sites/sessions');
session_start();

//check if role is not admin then redirect to login page
if($_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}
//----------------------------------------------------------------------------------------------------------------------