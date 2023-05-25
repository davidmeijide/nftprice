<?php
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
        <form class="form-group m-auto container-sm" action="/auth" method="post">
        <h2>Log in</h2>
        <label for="username">Username</label>
        <input class="form-control" type="text" name="username" id="username" required><br>

        <label for="password">Password</label>
        <input class="form-control" type="password" name="password" id="password" required><br>

        <button class="btn btn-info mb-2" name="login" value="login">Login</button>
        <p class="">Not registered? <a class="ml-2" href="/register">Register</a></p>
    </form>
    </div>
</body>

</html>