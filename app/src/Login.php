<?php

class Login{
    private $username;
    private $password;
    private $hashed_password; 
    private $email;
    private $logged_in = false;
    private $creation_date;
    private $last_login;
    private $role;

    public $register_success = false;
    public $errors = array();
    public $messages = array();
    
    public function __construct($username, $password, $email){
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;

        //COOKIES
 /*     if(isset($_COOKIE['username'])) $this->username = $_COOKIE['username'];
        if(isset($_COOKIE['email'])) $this->email = $_COOKIE['email']; */
        

    }

    public function login(){
        try{
            $connection = new Connection();

            //Check if user exists
            $pdoStatement = $connection->prepare("SELECT username, role, password FROM users
                                        WHERE username LIKE :username");
            $pdoStatement->bindParam(':username',$this->username);
            $pdoStatement->execute();
            if($pdoStatement->rowCount()==0) {
                $this->errors[] = $pdoStatement->rowCount();

                /* $this->errors[] = "Username does not exist"; */
                return false;
            }

            //Check if password is correct
            $result = $pdoStatement->fetch(PDO::FETCH_ASSOC);
            if(password_verify($this->password, $result['password'])){
                @session_start();
                $this->logged_in = true;
                $this->last_login = date("Y-m-d H:i:s");
                //Insert last login date pending
                $_SESSION['username'] = $this->username;
                $_SESSION['role'] = $result['role'];
                session_regenerate_id(true);
                return true;
            } 
            else return false;
        }
        catch(PDOException $e){
            die("Database error at login: ".$e->getMessage());
        }
    }
    public function register(){
        try{
            $connection = new Connection();
            //Check if user exists
            $pdoStatement = $connection->prepare("SELECT username, password FROM users
                                        WHERE username LIKE :username");
            $pdoStatement->bindParam(':username',$this->username);
            $pdoStatement->execute();
            //User exists
            if($pdoStatement->rowCount()>0) {
                $this->errors[] = "Username already exists";
                return false;
            }
            //Does not exist
            else{
                $pdoStatement = $connection->prepare("INSERT INTO users (username,password,creation_date,role,email)
                                                VALUES(:username,:password,:creation_date,:role,:email)");
                $pdoStatement->bindParam(':username',$this->username);
                $this->hashed_password = password_hash($this->password,PASSWORD_DEFAULT);
                $pdoStatement->bindParam(':password',$this->hashed_password);
                $this->creation_date = date("Y-m-d H:i:s");
                $pdoStatement->bindParam(':creation_date', $this->creation_date);
                $pdoStatement->bindParam(':role',$this->role);
                $pdoStatement->bindParam(':email',$this->email);

                $pdoStatement->execute();
                $this->register_success = true;
                return true;
            }
        }
        catch(PDOException $e){
            die("Database error at registering: ".$e->getMessage());
        }
    }
    public function validateRegister(){
        if (empty($_POST['username'])) {
            $this->errors[] = "Empty Username";
            return false;

        } elseif (empty($_POST['user_password_new']) || empty($_POST['user_password_repeat'])) {
            $this->errors[] = "Empty Password";
            return false;

        } elseif ($_POST['user_password_new'] != $_POST['user_password_repeat']) {
            $this->errors[] = "Passwords do not match";
            return false;

        }
        return true;
        
        
    }
}