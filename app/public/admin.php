<?php
@session_start();
include_once("../src/Admin.php");
include_once("../view/adminView.php");
include_once("../view/homeView.php");
if(!isset($_SESSION['username'])){
    header('Location: /login');    
}
if(!$_SESSION['role'] == "admin"){
    header('Location: /home');
}
showHeadIndex();
echo showUsersTable(json_decode(Admin::getUsers()));

