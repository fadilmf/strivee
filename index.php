<?php

session_start();

require "functions.php";

// Variable user
$user = isset($_SESSION["userId"]) ? getUserById($_SESSION["userId"]) : null;

$page = "Home";
require "template/start.php";

?>
<?php
$products = query("SELECT * FROM `products` WHERE `promoted`=true");
?>
<div class="row mt-4">
    <div class="col">
        <h4>Jasa Dipromosikan</h4>
    </div>
    <div class="col-auto">
        <a href="products.php" class="text-black-50 text-decoration-none">Lihat Selengkapnya</a>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="row product-list">
            <!-- Loop jasa -->
            <?php foreach ($products as $product) : ?>
                <?php

                $owner = getUserById($product["owner"]);
                if (!$owner) {
                    http_response_code(500);
                    echo "Something went wrong";
                    exit();
                }

                ?>
                <a href="jasa.php?id=<?= $product["id"] ?>" class="text-decoration-none text-dark col-md-6 col-lg-3">
                    <div class="card bg-light">
                        <img src="<?= $product["image"] ?>" class="card-img-top">
                        <div class="card-body">
                            <img src="<?= $owner["image"] ?>" class="rounded-circle" width="25">
                            <span><?= $owner["username"] ?></span>
                            <h5 class="card-title mt-2"><?= $product["name"] ?></h5>
                            <p><?= substr($product["description"], 0, 50) ?>...</p>
                            <div class="row">
                                <div class="col">
                                    <b><?= $product["city"] ?></b>
                                </div>
                                <div class="col-auto text-success">
                                    <?= $product["online"] ? '<i class="fa-solid fa-earth-americas"></i> Daring' : "" ?>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col">
                                    <span class="text-warning"><i class="fa fa-star"></i> <?= $product["rating"] > 0 ? round($product["rating"] / $product["rating_count"], 1) : "0" ?></span>
                                    <span class="text-black-50">(<?= $product["rating_count"] ?>)</span>
                                </div>
                                <div class="col-auto">
                                    <span><?= money($product["price"]) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            <?php endforeach ?>
        </div>
    </div>
</div>
<!-- Jika user sudah login maka tampilkan jasa di kota user -->
<?php if (isset($user) && $user["city_id"] > 0) :
    $products = getProductsWithFilter("`city_id`=" . $user["city_id"]);
?>
    <div class="row mt-4">
        <div class="col">
            <h4>Jasa Terpopuler di <?= $user["city"] ?></h4>
        </div>
        <div class="col-auto">
            <a href="products.php?city=1" class="text-black-50 text-decoration-none">Lihat Selengkapnya</a>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row product-list">
                <!-- Loop jasa -->
                <?php foreach ($products as $product) : ?>
                    <?php

                    $owner = getUserById($product["owner"]);
                    if (!$owner) {
                        http_response_code(500);
                        echo "Something went wrong";
                        exit();
                    }

                    ?>
                    <a href="jasa.php?id=<?= $product["id"] ?>" class="text-decoration-none text-dark col-md-6 col-lg-3">
                        <div class="card bg-light">
                            <img src="<?= $product["image"] ?>" class="card-img-top">
                            <div class="card-body">
                                <img src="<?= $owner["image"] ?>" class="rounded-circle" width="25">
                                <span><?= $owner["username"] ?></span>
                                <h5 class="card-title mt-2"><?= $product["name"] ?></h5>
                                <p><?= substr($product["description"], 0, 50) ?>...</p>
                                <div class="row">
                                    <div class="col">
                                        <b><?= $product["city"] ?></b>
                                    </div>
                                    <div class="col-auto text-success">
                                        <?= $product["online"] ? '<i class="fa-solid fa-earth-americas"></i> Daring' : "" ?>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col">
                                        <span class="text-warning"><i class="fa fa-star"></i> <?= $product["rating"] > 0 ? round($product["rating"] / $product["rating_count"], 1) : "0" ?></span>
                                        <span class="text-black-50">(<?= $product["rating_count"] ?>)</span>
                                    </div>
                                    <div class="col-auto">
                                        <span><?= money($product["price"]) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach ?>
            </div>
        </div>
    </div>
<?php
endif;
// Jasa terpopuler
$products = getProducts();
?>
<div class="row mt-4">
    <div class="col">
        <h4>Jasa Terpopuler</h4>
    </div>
    <div class="col-auto">
        <a href="products.php" class="text-black-50 text-decoration-none">Lihat Selengkapnya</a>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="row product-list">
            <!-- Loop jasa -->
            <?php foreach ($products as $product) : ?>
                <?php

                $owner = getUserById($product["owner"]);
                if (!$owner) {
                    http_response_code(500);
                    echo "Something went wrong";
                    exit();
                }

                ?>
                <a href="jasa.php?id=<?= $product["id"] ?>" class="text-decoration-none text-dark col-md-6 col-lg-3">
                    <div class="card bg-light">
                        <img src="<?= $product["image"] ?>" class="card-img-top">
                        <div class="card-body">
                            <img src="<?= $owner["image"] ?>" class="rounded-circle" width="25">
                            <span><?= $owner["username"] ?></span>
                            <h5 class="card-title mt-2"><?= $product["name"] ?></h5>
                            <p><?= substr($product["description"], 0, 50) ?>...</p>
                            <div class="row">
                                <div class="col">
                                    <b><?= $product["city"] ?></b>
                                </div>
                                <div class="col-auto text-success">
                                    <?= $product["online"] ? '<i class="fa-solid fa-earth-americas"></i> Daring' : "" ?>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col">
                                    <span class="text-warning"><i class="fa fa-star"></i> <?= $product["rating"] > 0 ? round($product["rating"] / $product["rating_count"], 1) : "0" ?></span>
                                    <span class="text-black-50">(<?= $product["rating_count"] ?>)</span>
                                </div>
                                <div class="col-auto">
                                    <span><?= money($product["price"]) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            <?php endforeach ?>
        </div>
    </div>
</div>
<script>
    // Script untuk mengscroll list jasa
    const productLists = document.querySelectorAll(".product-list");

    productLists.forEach((productList) => {
        let left = 0;
        let X = 0;
        let dragging = false;

        productList.addEventListener("wheel", (e) => {
            e.preventDefault();
            if (e.deltaY > 0) productList.scrollLeft += 100;
            else productList.scrollLeft -= 100;
        });

        productList.addEventListener("mousedown", (e) => {
            e.preventDefault();

            left = productList.scrollLeft;
            X = e.clientX;

            document.addEventListener("mousemove", mousemove);
            document.addEventListener("mouseup", mouseup);
        });

        productList.childNodes.forEach((product) => {
            product.addEventListener("click", (e) => {
                if (dragging) {
                    e.preventDefault();
                    dragging = false;
                }
            });
        });

        const mousemove = (e) => {
            const dx = e.clientX - X;
            if (dx !== 0) dragging = true;
            productList.scrollLeft = left - dx;
        };

        const mouseup = (e) => {
            document.removeEventListener("mousemove", mousemove);
            document.removeEventListener("mouseup", mouseup);
        };
    });
</script>
<?php require "template/end.php" ?>