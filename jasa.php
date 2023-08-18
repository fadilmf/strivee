<?php

session_start();

// Jika tidak ada id redirect ke home
if (!isset($_GET["id"])) {
    http_response_code(404);
    header("Location: /");
    exit();
}

require "functions.php";

// Variable jasa
$product = getProductById($_GET["id"]);
// Jika tidak menemukan jasa redirect ke home
if (!$product) {
    http_response_code(404);
    header("Location: /");
    exit();
}

// variable user
$user = isset($_SESSION["userId"]) ? getUserById($_SESSION["userId"]) : null;

if (isset($_POST["rating"]) && isset($_POST["message"]) && isset($user)) {
    // Memposting testimoni
    $result = addRating($product["id"], $user["id"], $_POST);
    if ($result["success"]) {
        $success = "Testimoni berhasil ditambahkan";
        $product = getProductById($_GET["id"]);
    } else {
        $error = $result["error"];
    }
}

// Owner jasa
$owner = getUserById($product["owner"]);
if (!$owner) {
    http_response_code(500);
    echo "Something went wrong";
    exit();
}

// Testimoni jasa
$ratings = getRatingsByProductId($product["id"]);

$isPromoted = query("SELECT * FROM `promotes` WHERE `product`=" . $product["id"]);

if (isset($_GET["action"]) && !$isPromoted) {
    mysqli_query(conn(), "INSERT INTO `promotes` (`product`) VALUES (" . $product["id"] . ")");
    $isPromoted = true;
}

$page = $product["name"];
require "template/start.php";

?>
<div class="row">
    <div class="col-lg">
        <h1><?= $product["name"] ?></h1>
        <img src="<?= $product["image"] ?>" class="mt-2 w-100">
    </div>
    <div class="col-lg mt-5 mt-lg-0">
        <img src="<?= $owner["image"] ?>" class="rounded-circle" width="50">
        <span><?= $owner["username"] ?></span>
        <h5 class="mt-2"><?= money($product["price"]) ?></h5>
        <h4 class="mt-2">Deskripsi</h4>
        <?php foreach (explode("\n", $product["description"]) as $line) echo "<p>$line</p>" ?>
        <h4>Daerah</h4>
        <p id="city"><?= $owner["city"] ?></p>
        <h4>Kategori</h4>
        <p><?= getCategories()[$product["category"]] ?></p>
        <?php if (isset($user)) : ?>
            <?php if ($owner["id"] !== $user["id"]) : ?>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modal">
                    Pesan Jasa
                </button>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#report-modal">
                    Laporkan Jasa
                </button>
            <?php else : ?>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#delete-modal">
                    Hapus Jasa
                </button>
                <?= !$isPromoted ? '<a href="?id=' . $product["id"] . '&action=promote" class="btn btn-primary">Promosikan Jasa Ini</a>' : "" ?>
            <?php endif ?>
        <?php else : ?>
            <span class="fst-italic fw-light">Login untuk memesan jasa</span>
        <?php endif ?>
    </div>
</div>
<h4 class="mt-3">
    Testimoni
    <span class="text-warning"><i class="fa fa-star"></i> <?= $product["rating"] > 0 ? round($product["rating"] / $product["rating_count"], 1) : "0" ?></span>
    <span class="text-black-50">(<?= $product["rating_count"] ?>)</span>
</h4>
<?= (isset($error)) ? '<div class="alert alert-danger" role="alert">' . $error . '</div>' : "" ?>
<?= (isset($success)) ? '<div class="alert alert-success" role="alert">' . $success . '</div>' : "" ?>
<?php foreach ($ratings as $rating) : ?>
    <?php

    $author = getUserById($rating["author"]);

    ?>
    <div class="card mb-2">
        <div class="card-body">
            <div class="row">
                <div class="col-auto"><img src="<?= $author["image"] ?>" class="rounded-circle" width="50"></div>
                <div class="col">
                    <span class="fa fa-star<?= $rating["rating"] >= 1 ? " text-warning" : "" ?>"></span>
                    <span class="fa fa-star<?= $rating["rating"] >= 2 ? " text-warning" : "" ?>"></span>
                    <span class="fa fa-star<?= $rating["rating"] >= 3 ? " text-warning" : "" ?>"></span>
                    <span class="fa fa-star<?= $rating["rating"] >= 4 ? " text-warning" : "" ?>"></span>
                    <span class="fa fa-star<?= $rating["rating"] >= 5 ? " text-warning" : "" ?>"></span>
                    <br>
                    <span><?= $rating["message"] ?></span>
                    <span class="fst-italic fw-light">~ <?= $author["username"] ?></span>
                </div>
            </div>
        </div>
    </div>
<?php endforeach ?>

<?php if (isset($user)) : ?>
    <?php if ($owner["id"] === $user["id"]) : ?>
        <!-- Delete Modal -->
        <div class="modal fade" id="delete-modal" tabindex="-1" aria-labelledby="delete-modal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <div class="container">
                            Apakah anda yakin ingin menghapus jasa ini?
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <a href="delete.php?id=<?= $product["id"] ?>" class="btn btn-danger">Hapus</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif ?>

    <!-- Modal -->
    <div class="modal fade" id="modal" tabindex="-1" aria-labelledby="modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <div class="container">
                        Anda akan memesan jasa <b><?= $product["name"] ?></b> di <b><?= $owner["city"] ?></b> yang di sediakan oleh <b><?= $owner["username"] ?></b>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modal2">Lanjutkan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal 2 -->
    <div class="modal fade" id="modal2" tabindex="-1" aria-labelledby="modal2" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <div class="container">
                        <h4><?= $owner["username"] ?></h4>
                        <a href="https://api.whatsapp.com/send?phone=62<?= $owner["phone"] ?>&text=<?= urlencode("Saya ingin pesan jasa *" . $product["name"] . "*") ?>" target="_blank" class="btn btn-light mt-2"><i class="fa-brands fa-whatsapp"></i> Whatsapp</a>
                        <span class="btn btn-light mt-2"><i class="fa-solid fa-phone"></i> +62 <?= $owner["phone"] ?></span>
                        <a href="#" class="btn btn-light mt-2"><i class="fa-brands fa-facebook"></i> Facebook</a>
                    </div>
                </div>
                <div class="modal-footer">
                    <?php if (!getRatingByProductIdAndAuthorId($product["id"], $user["id"])) : ?>
                        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#add-rating">Tambah Testimoni</button>
                    <?php endif ?>
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">Selesai</button>
                </div>
            </div>
        </div>
    </div>

    <?php if (!getRatingByProductIdAndAuthorId($product["id"], $user["id"])) : ?>
        <div class="modal fade" id="add-rating" tabindex="-1" aria-labelledby="add-rating" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" id="rating-form">
                        <div class="modal-header">
                            <h5 class="modal-title">Tambah Testimoni Anda</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="container">
                                <div class="d-flex justify-content-center">
                                    <span class="fa fa-star fs-1"></span>
                                    <span class="fa fa-star fs-1"></span>
                                    <span class="fa fa-star fs-1"></span>
                                    <span class="fa fa-star fs-1"></span>
                                    <span class="fa fa-star fs-1"></span>
                                </div>
                                <input type="hidden" name="rating" id="rating">
                                <textarea name="message" class="form-control mt-3" placeholder="Komentar" rows="3" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">Tambah</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script>
            // Rating
            const stars = document.querySelectorAll(".fa-star.fs-1");
            const rating = document.querySelector("#rating");
            const form = document.querySelector("#rating-form");

            stars.forEach((star, i) => {
                star.addEventListener("click", () => {
                    rating.value = i + 1;
                    stars.forEach((starr, id) => id <= i ? starr.classList.add("text-warning") : starr.classList.remove("text-warning"));
                });
            });

            form.addEventListener("submit", (e) => {
                if (!rating.value) e.preventDefault();
            });
        </script>
    <?php endif ?>
    <!-- report modal -->
    <div class="modal fade" id="report-modal" tabindex="-1" aria-labelledby="report-modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <form method="GET" action="report.php">
                    <div class="modal-header">
                        <h5 class="modal-title">Laporkan Jasa Ini</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <label for="reason">Alasan</label>
                            <input type="hidden" name="id" value="<?= $product["id"] ?>">
                            <input type="text" class="form-control" id="reason" name="reason">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Laporkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif ?>
<?php require "template/end.php" ?>