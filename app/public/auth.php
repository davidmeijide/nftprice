<?php
include_once('../src/Bot.php');
include_once('../src/Login.php');
$bot = new Bot("");
//Login
if(isset($_POST['login'])){
    $login = new Login(htmlspecialchars($_POST['username']),htmlspecialchars($_POST['password']),"");
    //User and password validation pending
    if($login->login()==true) header('Location: /home');
    else{
        /* print_r($login->errors); */
        header('Location: /public/login'); 
    } 
    
}
if(isset($_POST['register'])){
    //User and password validation
    $login = new Login(trim(htmlspecialchars($_POST['username'])),htmlspecialchars($_POST['user_password_new']),trim(htmlspecialchars($_POST['email'])));
    /* print_r($login); */
    if($login->validateRegister() == true){
        $login->register();
        $login = new Login(htmlspecialchars($_POST['username']),htmlspecialchars($_POST['user_password_new']),"");
        //User and password validation pending
        if($login->login()==true) header('Location: /home');
        else{
            /* print_r($login->errors); */
            header('Location: /login'); 
        } 
    }
    else{
        $_SESSION['errors'] = $login->errors;
        include('../view/registerView');
        //header('Location: register.html');

    }

}

