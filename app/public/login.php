<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>NFTtrack</h1>
    </header>
    <div class="d-flex container-sm flex-column">
        <form class="form-group m-auto container-sm" action="/public/auth.php" method="post">
        <h2>Log in</h2>
        <label for="username">Username</label>
        <input class="form-control" type="text" name="username" id="username" required><br>

        <label for="password">Password</label>
        <input class="form-control" type="password" name="password" id="password" required><br>

        <button class="btn btn-info mb-2" name="login" value="login">Login</button>
        <p class="">Not registered? <a class="ml-2" href="/public/register.php">Register</a></p>
    </form>
    </div>
</body>

</html>