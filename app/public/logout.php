<?php

if(isset($_COOKIE['username'])){
    unset($_COOKIE['username']);
    unset($_COOKIE['email']);
    unset($_COOKIE['logged_in']);
}
@session_start();
@session_unset();
@session_destroy();

header('Location: /public/login.php');    