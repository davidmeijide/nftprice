<?php
include_once('../view/footerView.php');
include_once('../view/homeView.php');
@session_start();
if(isset($_SESSION['username'])){
    header('Location: /home');
}
?>
<!DOCTYPE html>
<html lang="en">
<?php
    showHeadIndex();
?>
<body>
    <header>
        <a class="text-decoration-none text text-dark"  href="/index"><h1>NFTprice</h1></a>
    </header>
    <div class="d-flex container-sm flex-column">
        <form class="form-group m-auto container-sm" action="/auth" method="POST">
            <h2>Create an account</h2>

            <p class="error"></p>

            <label for="username">Username</label>
            <input class="form-control" type="text" name="username" id="username" required><br>
        
            <label for="pass">Password</label>
            <input class="form-control" type="password" name="user_password_new" id="user_password_new" required><br>
    
            <label for="pass">Repeat password</label>
            <input class="form-control" type="password" name="user_password_repeat" id="user_password_repeat" required><br>
        
            <label for="email">Email</label>
            <input class="form-control" type="email" name="email" id="email"><br>
            
            <button class="btn btn-info mb-2" id="register" name="register" value="register">Register</button>
            <p class="">Already registered? <a class="ml-2" href="/login">Log in</a></p>

        </form>   

    </div>
    <?php
    showFooter();
    ?>
</body>
</html>