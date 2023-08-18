<?php

session_start();

require "functions.php";

// pengecekan login
if (!isset($_SESSION["userId"])) {
    http_response_code(500);
    echo "You're not logged in";
    exit();
}

// pengecekan user
$user = getUserById($_SESSION["userId"]);
if (!$user) {
    http_response_code(500);
    echo "Something went wrong";
    exit();
}

// pengecekan id
if (!isset($_GET["id"])) {
    http_response_code(400);
    echo "No ID";
    exit();
}

// mendapatkan jasa dari database
$product = getProductById($_GET["id"]);
if (!$product) {
    http_response_code(404);
    echo "No Product";
    exit();
}

// mendapatkan owner jasa
$owner = getUserById($product["owner"]);
if (!$owner) {
    http_response_code(500);
    echo "Something went wrong";
    exit();
}

// jika user tidak sama dengan pemilik jasa maka tidak boleh menghapus jasa ini
if ($user["id"] !== $owner["id"] && !isLoggedInAsAdmin($_SESSION)) {
    http_response_code(403);
    echo "You're not allowed to do this";
    exit();
}

// mendapatkan semua laporan pada jasa ini
$reports = query("SELECT `id` FROM `reports` WHERE `product`=" . $product["id"]);
$promotes = query("SELECT `id` FROM `promotes` WHERE `product`=" . $product["id"]);

// menghapus dari database
mysqli_query(conn(), "DELETE FROM `products` WHERE `id`=" . $product["id"]);
// menghapus semua laporan pada jasa ini
foreach ($reports as $report) {
    mysqli_query(conn(), "DELETE FROM `reports` WHERE `product`=" . $product["id"]);
}
foreach ($promotes as $promote) {
    mysqli_query(conn(), "DELETE FROM `promotes` WHERE `product`=" . $product["id"]);
}
if (isset($_GET["admin"])) {
    header("Location: /admin/report.php");
    exit();
}
header("Location: profile.php");
