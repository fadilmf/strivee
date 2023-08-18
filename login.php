<?php

session_start();

if (isset($_SESSION["userId"])) {
    header("Location: /");
    exit();
}

require "functions.php";

if (isset($_POST["username"]) && isset($_POST["password"]) && !isset($_POST["password2"]) && !isset($_POST["email"])) {
    // Login
    $result = login($_POST);
    if ($result["success"]) {
        $_SESSION["userId"] = $result["id"];
        header("Location: /" . (isset($_GET["from"]) ? $_GET["from"] : ""));
    } else {
        $error = $result["error"];
    }
} elseif (isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["password2"]) && isset($_POST["email"])) {
    // Register
    $result = register($_POST);
    if ($result["success"]) {
        $_SESSION["userId"] = $result["id"];
        header("Location: /" . (isset($_GET["from"]) ? $_GET["from"] : "profile.php"));
    } else {
        $error2 = $result["error"];
    }
}

$page = "Login";
require "template/start.php";

?>
<!-- Navbar login -->
<nav class="navbar navbar-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">Strivee</a>
        <!-- Form login -->
        <form method="POST" class="d-flex">
            <div class="row">
                <div class="col-lg-auto">
                    <!-- Pesan error login -->
                    <?= (isset($error)) ? '<div class="alert alert-danger m-0" role="alert">' . $error . '</div>' : "" ?>
                </div>
                <div class="col-lg-auto mt-2 mt-lg-0">
                    <input type="text" class="form-control" placeholder="Username" name="username" required>
                </div>
                <div class="col-lg-auto mt-2 mt-lg-0">
                    <input type="password" class="form-control" placeholder="Password" name="password" required>
                </div>
                <div class="col-lg-auto mt-2 mt-lg-0">
                    <button type="submit" class="btn btn-success w-100 ">Login</button>
                </div>
            </div>
        </form>
    </div>
</nav>

<!-- Card register -->
<div class="card col-11 col-md-6 col-lg-4 col-xl-3 m-auto mt-5">
    <div class="card-body container">
        <!-- Form register -->
        <h2 class="card-title text-center">Register</h2>
        <!-- Pesan error register -->
        <?= (isset($error2)) ? '<div class="alert alert-danger" role="alert">' . $error2 . '</div>' : "" ?>
        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="password2" class="form-label">Konfirmasi Password</label>
                <input type="password" class="form-control" id="password2" name="password2" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Register</button>
        </form>
    </div>
</div>
<?php require "template/end.php" ?>