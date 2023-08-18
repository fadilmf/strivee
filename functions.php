<?php

// Function untuk mengkonversi angka ke rupiah
function money($number)
{
    return "Rp" . number_format($number, 0, ".", ".");
}

// Function untuk mengkonversi tulisan ke slug
function slug($text, $divider = "-")
{
    $text = preg_replace('~[^\pL\d]+~u', $divider, $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, $divider);
    $text = preg_replace('~-+~', $divider, $text);
    $text = strtolower($text);

    if (empty($text)) {
        return 'n-a';
    }

    return $text;
}

// Function untuk resize gambar
function resizeImage($file, $w, $h)
{
    list($width, $height) = getimagesize($file);
    $extension = explode(".", $file);
    $extension = strtolower(end($extension));
    if ($extension === "jpg" || $extension === "jpeg") $src = @imagecreatefromjpeg($file);
    else $src = @imagecreatefrompng($file);
    $dst = imagecreatetruecolor($w, $h);
    @imagecopyresampled($dst, $src, 0, 0, 0, 0, $w, $h, $width, $height);
    if ($extension === "jpg" || $extension === "jpeg") imagejpeg($dst, $file);
    else imagepng($dst, $file);
    return [
        "success" => true
    ];
}

// Function untuk mengupload file
function upload($file, $allowedFiles, $allowedTypes, $maxSize, $target, $targetName)
{
    $name = $file["name"];
    $type = $file["type"];
    $size = $file["size"];
    $tmp = $file["tmp_name"];
    $extension = explode(".", $name);
    $extension = strtolower(end($extension));

    if (!in_array($extension, $allowedFiles)) return [
        "success" => false,
        "error" => "File yang anda upload tidak diperbolehkan"
    ];

    if (!in_array($type, $allowedTypes)) return [
        "success" => false,
        "error" => "File yang anda upload tidak diperbolehkan"
    ];

    if ($size > $maxSize) return [
        "success" => false,
        "error" => "File yang anda upload melebihi batas maksimal ukuran file",
    ];

    $targetPath = $target . "/$targetName.$extension";

    return [
        "success" => move_uploaded_file($tmp, $targetPath),
        "error" => "Something went wrong (file)",
        "file" => $targetPath
    ];
}

// Function pagination
function pagination($page, $numRows, $limit = 10)
{
    $totalPages = ceil($numRows / $limit);
    $start = ($page - 1) * $limit;
    $pages = [];
    for ($i = $page > 2 ? $page - 2 : ($page > 1 ? $page - 1 : $page); $i <= ($totalPages - $page > 2 ? $page + 2 : ($totalPages - $page > 1 ? $page + 1 : $totalPages)); $i++) {
        $pages[] = $i;
    }

    return [
        "start" => $start,
        "page" => $page,
        "previous" => $page > 1 ? $page - 1 : null,
        "pages" => $pages,
        "next" => $page < $totalPages ? $page + 1 : null,
    ];
}

// Menyambungkan ke database
function conn()
{
    return mysqli_connect("localhost", "root", "", "ecommerce");
}

// Query SQL
function query($query)
{
    $conn = conn();
    $result = mysqli_query($conn, $query);

    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    return $rows;
}

// Function register
function register($data)
{
    $conn = conn();

    $username = htmlspecialchars(strtolower($_POST["username"]));
    $password = mysqli_real_escape_string($conn, $_POST["password"]);
    $password2 = mysqli_real_escape_string($conn, $_POST["password2"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);

    if (empty($username) || empty($password) || empty($password2) || empty($email)) return [
        "success" => false,
        "error" => "Nama atau password tidak boleh kosong"
    ];

    if (strlen($username) < 5 || strlen($username) > 20) return [
        "success" => false,
        "error" => "Nama Pengguna harus lebih dari 5 kata dan kurang dari 20 kata"
    ];

    if (query("SELECT * FROM `users` WHERE `username`='$username'")) return [
        "success" => false,
        "error" => "Nama pengguna sudah dipakai"
    ];

    if ($password !== $password2) return [
        "success" => false,
        "error" => "Password tidak sama dengan konfirmasi password"
    ];

    if (strlen($password) < 8 || strlen($password) > 20) return [
        "success" => false,
        "error" => "Password harus lebih dari 8 kata dan kurang dari 20 kata"
    ];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return [
        "success" => false,
        "error" => "Email tidak valid"
    ];

    if (query("SELECT * FROM `users` WHERE `email`='$email'")) return [
        "success" => false,
        "error" => "Email sudah dipakai"
    ];

    $newPassword = password_hash($password, PASSWORD_DEFAULT);
    mysqli_query($conn, "INSERT INTO `users` (`username`, `password`, `email`, `image`) VALUES ('$username', '$newPassword', '$email', 'img/pfp.jpg')");

    if (mysqli_error($conn) || mysqli_affected_rows($conn) < 1) return [
        "success" => false,
        "error" => "Something went wrong (1)"
    ];

    return [
        "success" => true,
        "id" => mysqli_insert_id($conn)
    ];
}

// Function login
function login($data)
{
    $conn = conn();

    $username = htmlspecialchars(strtolower($_POST["username"]));
    $password = mysqli_real_escape_string($conn, $_POST["password"]);

    if (empty($username) || empty($password)) return [
        "success" => false,
        "error" => "Username atau password tidak boleh kosong"
    ];

    $user = query("SELECT * FROM `users` WHERE `username`='$username'");

    if (!$user) return [
        "success" => false,
        "error" => "Username atau password salah"
    ];

    if (!password_verify($password, $user[0]["password"])) return [
        "success" => false,
        "error" => "Username atau password salah"
    ];

    return [
        "success" => true,
        "id" => $user[0]["id"]
    ];
}

// Mendapatkan kota dari id
function getCityById($cityId)
{
    $city = @file_get_contents("https://dev.farizdotid.com/api/daerahindonesia/kota/$cityId");
    if (!$city) return false;
    return json_decode($city, true)["nama"];
}

function getUsers($start = 0)
{
    return query("SELECT * FROM `users` LIMIT $start, 10");
}

function getUserById($userId)
{
    $user = query("SELECT * FROM `users` WHERE `id`=$userId");
    if (!$user) return null;
    return $user[0];
}

function editProfile($userId, $data)
{
    $conn = conn();

    $user = getUserById($userId);

    $name = mysqli_real_escape_string($conn, htmlspecialchars($data["name"]));
    $gender = $data["gender"] ? "true" : "false";
    $address = mysqli_real_escape_string($conn, $data["address"]);
    $phone = mysqli_real_escape_string($conn, $data["phone"]);
    $email = mysqli_real_escape_string($conn, $data["email"]);
    $image = $_FILES["image"];

    if ($phone) {
        if (!is_numeric($phone)) return [
            "success" => false,
            "error" => "Nomor HP harus berupa angka"
        ];

        if ($phone < 8100000000 || $phone > 819999999999) return [
            "success" => false,
            "error" => "Masukan nomor HP dengan benar"
        ];

        if (query("SELECT * FROM `users` WHERE `phone`='$phone' AND `id`!=$userId")) return [
            "success" => false,
            "error" => "Nomor HP sudah dipakai"
        ];
    }

    if (empty($email)) return [
        "success" => false,
        "error" => "Email tidak boleh kosong"
    ];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return [
        "success" => false,
        "error" => "Email tidak valid"
    ];

    if (query("SELECT * FROM `users` WHERE `email`='$email' AND `id`!=$userId")) return [
        "success" => false,
        "error" => "Email sudah dipakai"
    ];

    if (!empty($image["name"])) {
        if ($user["image"] && $user["image"] !== "img/pfp.jpg") {
            unlink($user["image"]);
        }

        $uploadedImage = upload($image, ["jpg", "jpeg", "png"], ["image/jpeg", "image/png"], 5e+6, "img/users", uniqid(slug($user["username"]) . "-"));
        if (!$uploadedImage["success"]) return $uploadedImage;
        $uploadedImage = $uploadedImage["file"];
        $resized = resizeImage($uploadedImage, 400, 400);
        if (!$resized["success"]) return $resized;
    } else {
        $uploadedImage = $user["image"];
    }

    if (isset($_POST["city"])) {
        $cityId = $_POST["city"];
        $city = getCityById($cityId);
        if (!$city) return [
            "success" => false,
            "error" => "Masukan kota dengan benar"
        ];
        mysqli_query($conn, "UPDATE `products` SET `city_id`=$cityId,`city`='$city' WHERE `owner`=$userId");
        mysqli_query($conn, "UPDATE `users` SET `email`='$email',`name`='$name',`gender`=$gender,`address`='$address',`phone`='$phone',`image`='$uploadedImage',`city_id`=$cityId,`city`='$city' WHERE `id`=$userId");
    } else {
        mysqli_query($conn, "UPDATE `users` SET `email`='$email',`name`='$name',`gender`=$gender,`address`='$address',`phone`='$phone',`image`='$uploadedImage' WHERE `id`=$userId");
    }

    if (mysqli_error($conn)) return [
        "success" => false,
        "error" => "Something went wrong (1)"
    ];

    return [
        "success" => true,
        "id" => mysqli_insert_id($conn)
    ];
}

function changePassword($userId, $data)
{
    $conn = conn();

    $password = mysqli_real_escape_string($conn, $_POST["password"]);
    $password2 = mysqli_real_escape_string($conn, $_POST["password2"]);

    if (empty($password) || empty($password2)) return [
        "success" => false,
        "error" => "Password tidak boleh kosong"
    ];

    $user = query("SELECT * FROM `users` WHERE `id`=$userId");

    if (!$user) return [
        "success" => false,
        "error" => "Something went wrong (1)"
    ];

    if (!password_verify($password, $user[0]["password"])) return [
        "success" => false,
        "error" => "Password lama salah"
    ];

    if (strlen($password2) < 8 || strlen($password2) > 20) return [
        "success" => false,
        "error" => "Password baru harus lebih dari 8 kata dan kurang dari 20 kata"
    ];

    $newPassword = password_hash($password2, PASSWORD_DEFAULT);

    mysqli_query($conn, "UPDATE `users` SET `password`='$newPassword' WHERE `id`=$userId");

    if (mysqli_error($conn) || mysqli_affected_rows($conn) < 1) return [
        "success" => false,
        "error" => "Something went wrong (2)"
    ];

    return [
        "success" => true,
        "id" => mysqli_insert_id($conn)
    ];
}

function getCategories()
{
    return [
        "Pekerjaan Rumah",
        "Tukang",
        "Kesehatan",
        "Desain & Grafis",
        "Musik & Suara",
        "Pemrograman & Teknologi",
        "Pendidikan",
        "Media Sosial / Hiburan",
        "Lainnya"
    ];
}

function getProducts($start = 0)
{
    return query("SELECT * FROM `products` ORDER BY `rating` DESC LIMIT $start, 10");
}

function getProudctsByOwner($userId, $start = 0)
{
    return query("SELECT * FROM `products` WHERE `owner`=$userId ORDER BY `rating` DESC LIMIT $start, 10");
}

function getProductsWithFilter($filter, $start = 0)
{
    return query("SELECT * FROM `products` WHERE $filter ORDER BY `rating` DESC LIMIT $start, 10");
}


function getProductById($productId)
{
    $product = query("SELECT * FROM `products` WHERE `id`=$productId");
    if (!$product) return null;
    return $product[0];
}

function createProduct($userId, $data)
{
    $conn = conn();

    $user = getUserById($userId);

    $name = mysqli_real_escape_string($conn, htmlspecialchars($data["name"]));
    $category = $data["category"];
    $price = $data["price"];
    $description = mysqli_real_escape_string($conn, htmlspecialchars($data["description"]));
    $image = $_FILES["image"];
    $online = isset($_POST["online"]) ? "true" : "false";

    if (empty($user["phone"]) || empty($user["city"])) return [
        "success" => false,
        "error" => "Anda harus mengisi nomor hp dan kota sebelum membuat jasa"
    ];

    if (empty($name) || !is_numeric($category) || empty($price) || empty($description) || empty($image) || $image["error"] == 4) return [
        "success" => false,
        "error" => "Nama, kategori, harga, deskripsi, dan gambar tidak boleh kosong"
    ];

    if (strlen($name) < 5 || strlen($name) > 20) return [
        "success" => false,
        "error" => "Nama harus lebih dari 5 dan kurang dari 20 kata"
    ];

    if ($category < 0 || $category >= count(getCategories())) return [
        "success" => false,
        "error" => "Pilih kategori dengan benar"
    ];

    if (!is_numeric($price)) return [
        "success" => false,
        "error" => "Harga harus bentuk angka"
    ];

    if ($price < 500 || $price > 100000000) return [
        "success" => false,
        "error" => "Harga harus lebih dari Rp500 dan kurang dari Rp100.000.000"
    ];

    if (strlen($description) < 50 || strlen($description) > 1000) return [
        "success" => false,
        "error" => "Deskripsi harus lebih dari 50 kata dan kurang dari 1000 kata"
    ];

    $uploadedImage = upload($image, ["jpg", "jpeg", "png"], ["image/jpeg", "image/png"], 5e+6, "img/products", uniqid(slug($name) . "-"));
    if (!$uploadedImage["success"]) return $uploadedImage;
    $uploadedImage = $uploadedImage["file"];
    $resized = resizeImage($uploadedImage, 1024, 576);
    if (!$resized["success"]) return $resized;

    mysqli_query($conn, "INSERT INTO `products` (`owner`, `name`, `category`, `price`, `description`, `image`, `city_id`, `city`, `online`) VALUES ($userId,'$name',$category,$price,'$description','$uploadedImage'," . $user["city_id"] . ",'" . $user["city"] . "',$online)");

    if (mysqli_error($conn) || mysqli_affected_rows($conn) < 1) return [
        "success" => false,
        "error" => "Something went wrong (1) " . mysqli_error($conn)
    ];

    return [
        "success" => true,
        "id" => mysqli_insert_id($conn)
    ];
}

function getRatingsByProductId($productId, $start = 0)
{
    return query("SELECT * FROM `ratings` WHERE `product`=$productId LIMIT $start, 10");
}

function getRatingByProductIdAndAuthorId($productId, $authorId)
{
    $rating = query("SELECT * FROM `ratings` WHERE `product`=$productId AND `author`=$authorId");
    if (!$rating) return null;
    return $rating[0];
}

function addRating($productId, $authorId, $data)
{
    $conn = conn();

    $rating = $data["rating"];
    $message = $data["message"];

    if (getRatingByProductIdAndAuthorId($productId, $authorId)) return [
        "success" => false,
        "error" => "Anda sudah memberikan testimoni"
    ];

    if (empty($rating) || empty($message)) return [
        "success" => false,
        "error" => "Rating dan komentar tidak boleh kosong"
    ];

    if (!is_numeric($rating) || $rating < 1 || $rating > 5) return [
        "success" => false,
        "error" => "Masukan rating dengan benar"
    ];

    if (strlen($message) < 5 || strlen($message) > 100) return [
        "success" => false,
        "error" => "Komentar harus lebih dari 5 kata dan kurang dari 100 kata"
    ];

    mysqli_query($conn, "INSERT INTO `ratings` (`product`,`author`,`rating`,`message`) VALUE ($productId, $authorId, $rating, '$message')");
    mysqli_query($conn, "UPDATE `products` SET `rating`=`rating`+$rating,`rating_count`=`rating_count`+1 WHERE `id`=$productId");

    if (mysqli_error($conn) || mysqli_affected_rows($conn) < 1) return [
        "success" => false,
        "error" => "Something went wrong (1) " . mysqli_error($conn)
    ];

    return [
        "success" => true,
        "id" => mysqli_insert_id($conn)
    ];
}
