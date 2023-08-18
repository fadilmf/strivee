<?php

session_start();

require "../functions.php";

// pengecekan admin
if (!isLoggedInAsAdmin($_SESSION)) {
    http_response_code(500);
    echo "You're not logged in as admin";
    exit();
}

// pengecekan id
if (!isset($_GET["id"])) {
    http_response_code(400);
    echo "No ID";
    exit();
}

// mengecek apakah user ada di database
$user = getUserById($_GET["id"]);
if (!$user) {
    http_response_code(404);
    echo "No User";
    exit();
}

// mendapatkan jasa yang dimiliki user
$products = getProudctsByOwner($user["id"]);

// menghapus user dari database
// menghapus semua jasa yang dimiliki user
foreach ($products as $product) {
    mysqli_query(conn(), "DELETE FROM `products` WHERE `id`=" . $product["id"]);
}
// redirect ke users.php
header("Location: users.php");
