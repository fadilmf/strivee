<?php

session_start();

require "functions.php";

// mengecek login atau tidak
if (!isset($_SESSION["userId"])) {
    http_response_code(500);
    echo "You're not logged in";
    exit();
}

// mengecek jika user ada
$user = getUserById($_SESSION["userId"]);
if (!$user) {
    http_response_code(500);
    echo "Something went wrong";
    exit();
}

// mengecek jika ada id
if (!isset($_GET["id"])) {
    http_response_code(400);
    echo "No ID";
    exit();
}

// mendapatkan jasa dari id
$product = getProductById($_GET["id"]);
if (!$product) {
    http_response_code(404);
    echo "No Product";
    exit();
}

// mendapatkan alasan
if (!isset($_GET["reason"])) {
    echo "No Reason";
    exit();
}

// jika alasan lebih dari 100 kata tidak boleh
if (strlen($_GET["reason"]) > 100) {
    echo "Reason 100 Limit";
    exit();
}

// masukan report ke database
mysqli_query(conn(), "INSERT INTO `reports` (`product`, `reporter`, `reason`) VALUES (" . $product["id"] . "," . $user["id"] . ",'" . $_GET["reason"] . "')");
echo "<script>
    alert('Jasa berhasil dilaporkan');
    location.href = '/';
</script>";
