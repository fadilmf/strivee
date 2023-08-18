<?php

session_start();

require "functions.php";

// Variable user
$user = isset($_SESSION["userId"]) ? getUserById($_SESSION["userId"]) : null;

// pagination
$pagination = pagination(isset($_GET["page"]) && is_numeric($_GET["page"]) ? $_GET["page"] : 1, query("SELECT COUNT(*) FROM `products`")[0]["COUNT(*)"]);

// Jika ada filter
if (isset($_GET["q"]) && $_GET["q"] || isset($_GET["city"]) && $_GET["city"] || isset($_GET["online"]) || isset($_GET["category"])) {
    $filter = "";
    // filter search
    if (isset($_GET["q"]) && $_GET["q"]) $filter .= "`name` LIKE '%" . $_GET["q"] . "%'";
    // filter kota
    if (isset($_GET["city"]) && $_GET["city"] && isset($user) && $user["city_id"] > 0) $filter .= ($filter ? " AND " : "") . "`city_id`=" . $user["city_id"];
    // filter daring
    if (isset($_GET["online"])) {
        switch ($_GET["online"]) {
            case 1:
                $filter .= ($filter ? " AND " : "") . "`online`=false";
                break;

            case 2:
                $filter .= ($filter ? " AND " : "") . "`online`=true";
                break;
        }
    }
    // Filter kategori
    if (isset($_GET["category"]) && is_numeric($_GET["category"]) && $_GET["category"] > 0) $filter .= $filter ? " AND `category`=" . ((int)$_GET["category"] - 1) : "`category`=" . ((int)$_GET["category"] - 1);
    if ($filter) {
        // filter pagination
        $pagination = pagination(isset($_GET["page"]) && is_numeric($_GET["page"]) ? $_GET["page"] : 1, query("SELECT COUNT(*) FROM `products` WHERE $filter")[0]["COUNT(*)"]);
        $products = getProductsWithFilter($filter, $pagination["start"]);
    } else $products = getProducts($pagination["start"]);
} else {
    // list produk
    $products = getProducts($pagination["start"]);
}

$page = "Jasa";
require "template/start.php";

?>
<div class="row">
    <div class="col">
        <h4><?= isset($_GET["q"]) && $_GET["q"] ? 'Hasil Pencarian "' . $_GET["q"] . '"' : "Jasa Terpopuler" ?> <?= isset($user) ? (isset($_GET["city"]) ? ($_GET["city"] && $user["city_id"] > 0 ? "di " . $user["city"] : "") : "") : "" ?></h4>
    </div>
    <div class="col-auto">
        <!-- Form filter -->
        <form id="filter-form">
            <div class="row">
                <!-- Jika user login dan sudah mengisi kota maka ada tombol filter kota -->
                <?php if ($user && $user["city_id"] > 0) : ?>
                    <div class="col-lg-auto mt-2 mt-lg-0">
                        <select name="city" class="form-select">
                            <option value="0">Semua Kota</option>
                            <option value="1" <?= isset($_GET["city"]) ? ($_GET["city"] ? "selected" : "") : "" ?>>Kota Saya</option>
                        </select>
                    </div>
                <?php endif ?>
                <!-- Filter daring -->
                <div class="col-lg-auto mt-2 mt-lg-0">
                    <select name="online" class="form-select">
                        <option value="0">Jasa Daring dan Tidak Daring</option>
                        <option value="1" <?= isset($_GET["online"]) ? ($_GET["online"] == 1 ? "selected" : "") : "" ?>>Jasa Tidak Daring</option>
                        <option value="2" <?= isset($_GET["online"]) ? ($_GET["online"] == 2 ? "selected" : "") : "" ?>>Jasa Daring</option>
                    </select>
                </div>
                <!-- Filter kategori -->
                <div class="col-lg-auto mt-2 mt-lg-0">
                    <select name="category" class="form-select">
                        <option value="0">Kategori</option>
                        <?php foreach (getCategories() as $id => $category) echo '<option value="' . ((int)$id + 1) . '" ' . (isset($_GET["category"]) ? ($_GET["category"] == $id + 1 ? "selected" : "") : "") . '>' . $category . '</option>' ?>
                    </select>
                </div>
                <div class="col-lg-auto mt-2 mt-lg-0">
                    <button type="submit" class="btn btn-success">Terapkan Filter</button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- tombol pagination -->
<nav class="mt-3">
    <ul class="pagination">
        <li class="page-item">
            <button type="button" class="page-link <?= !$pagination["previous"] ? "disabled" : "" ?>" onclick="changePage(<?= $pagination['previous'] ?>)" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </button>
        </li>
        <?php foreach ($pagination["pages"] as $page) : ?>
            <li class="page-item <?= $page == $pagination["page"] ? "active" : "" ?>"><button type="button" class="page-link" onclick="changePage(<?= $page ?>)"><?= $page ?></button></li>
        <?php endforeach ?>
        <li class="page-item">
            <button type="button" class="page-link <?= !$pagination["next"] ? "disabled" : "" ?>" onclick="changePage(<?= $pagination['next'] ?>)" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </button>
        </li>
    </ul>
</nav>
<div class="row">
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
        <a href="jasa.php?id=<?= $product["id"] ?>" class="text-decoration-none text-dark col-md-6 col-lg-3 mt-3">
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
<!-- tombol pagination -->
<nav class="mt-3">
    <ul class="pagination">
        <li class="page-item">
            <button type="button" class="page-link <?= !$pagination["previous"] ? "disabled" : "" ?>" onclick="changePage(<?= $pagination['previous'] ?>)" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </button>
        </li>
        <?php foreach ($pagination["pages"] as $page) : ?>
            <li class="page-item <?= $page == $pagination["page"] ? "active" : "" ?>"><button type="button" class="page-link" onclick="changePage(<?= $page ?>)"><?= $page ?></button></li>
        <?php endforeach ?>
        <li class="page-item">
            <button type="button" class="page-link <?= !$pagination["next"] ? "disabled" : "" ?>" onclick="changePage(<?= $pagination['next'] ?>)" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </button>
        </li>
    </ul>
</nav>
<script>
    // Function untuk mengganti page
    const changePage = (page) => {
        const searchParams = new URLSearchParams(window.location.search);
        searchParams.set("page", page);
        window.location.search = searchParams.toString();
    }

    // Membuat form filter agar tidak menghilangkan filter lainnya ketika di submit
    const filterForm = document.querySelector("#filter-form");

    filterForm.addEventListener("submit", (e) => {
        e.preventDefault();
        const searchParams = new URLSearchParams(window.location.search);
        new FormData(filterForm).forEach((value, key) => {
            searchParams.set(key, value);
        });
        window.location.search = searchParams.toString();
    });
</script>
<?php require "template/end.php" ?>