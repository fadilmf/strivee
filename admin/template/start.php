<?php

$promoteActive = $page === "Promosi" ? "active" : "text-black";
$reportActive = $page === "Report" ? "active" : "text-black";
$usersActive = $page === "Pengguna" ? "active" : "text-black";
$user = getUserById($_SESSION["userId"]);
$username = $user["username"];
$image = $user["image"];

$nav = <<<HTML
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="promote.php" class="nav-link {$promoteActive}" aria-current="page">
                <i class="fa-solid fa-bullhorn"></i>
                Promosi
            </a>
        </li>
        <li>
            <a href="report.php" class="nav-link {$reportActive}">
                <i class="fa-solid fa-flag"></i>
                Report
            </a>
        </li>
        <li>
            <a href="users.php" class="nav-link {$usersActive}">
                <i class="fa-solid fa-user"></i>
                Pengguna
            </a>
        </li>
        <li>
            <a href="/" class="nav-link text-black">
                <i class="fa-solid fa-house"></i>   
                Kembali ke Halaman Utama
            </a>
        </li>
    </ul>
    <hr>
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-black text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="/{$image}" alt="" width="32" height="32" class="rounded-circle me-2">
            <strong>{$username}</strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
            <li><a class="dropdown-item" href="/logout.php">Logout</a></li>
        </ul>
    </div>
HTML;

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="/css/style.css">

    <title>Strivee Admin - <?= $page ?></title>
</head>

<body>
    <div class="d-lg-flex">
        <nav class="navbar bg navbar-expand-lg navbar-light sticky-top d-block d-lg-none">
            <div class="container-fluid">
                <a class="navbar-brand" href="/admin">Strivee Admin</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse mt-3 pb-3" id="navbarNavAltMarkup">
                    <?= $nav ?>
                </div>
            </div>
        </nav>
        <div class="col-lg-3 col-xl-2 bg d-none d-lg-flex flex-column flex-shrink-0 p-3 sticky-top" style="height: 100vh;">
            <a href="/admin" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-black text-decoration-none">
                <span class="navbar-brand fs-4">Strivee Admin</span>
            </a>
            <hr>
            <?= $nav ?>
        </div>
        <div class="p-3">