<?php

session_start();

require "../functions.php";

if (!isLoggedInAsAdmin($_SESSION)) {
    header("Location: /login.php");
    exit();
}

if (isset($_GET["product"]) && isset($_GET["action"])) {
    if ($_GET["action"] === "decline" && isset($_GET["id"])) {
        mysqli_query(conn(), "DELETE FROM `promotes` WHERE `id`=" . $_GET["id"]);
        mysqli_query(conn(), "UPDATE `products` SET `promoted`=false WHERE `id`=" . $_GET["product"]);
    } elseif ($_GET["action"] === "accept" && isset($_GET["id"])) {
        mysqli_query(conn(), "DELETE FROM `promotes` WHERE `id`=" . $_GET["id"]);
        mysqli_query(conn(), "UPDATE `products` SET `promoted`=true WHERE `id`=" . $_GET["product"]);
    } elseif ($_GET["action"] === "delete") {
        mysqli_query(conn(), "UPDATE `products` SET `promoted`=false WHERE `id`=" . $_GET["product"]);
    }
}

$pagination = pagination(isset($_GET["page"]) && is_numeric($_GET["page"]) ? $_GET["page"] : 1, query("SELECT COUNT(*) FROM `promotes`")[0]["COUNT(*)"]);
$promotes = query("SELECT * FROM `promotes` LIMIT " . $pagination["start"] . ", 10");

$promotedProducts = query("SELECT * FROM `products` WHERE `promoted`=true");

$page = "Promosi";
require "template/start.php";

?>
<h1>Promosi</h1>
<h5>Permintaan Promosi</h5>
<!-- tabel jasa yang ingin di promosi -->
<div class="table-responsive">
    <table class="table" style="width: 100%;">
        <thead>
            <tr>
                <th scope="col">Nama Jasa</th>
                <th scope="col">Owner Jasa</th>
                <th scope="col">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <!-- loop promosi -->
            <?php foreach ($promotes as $promote) :

                $product = getProductById($promote["product"]);
                $owner = getUserById($product["owner"])["username"];

            ?>
                <tr>
                    <td><?= $product["name"] ?></td>
                    <td><?= $owner ?></td>
                    <td>
                        <a href="?id=<?= $promote["id"] ?>&product=<?= $product["id"] ?>&action=decline" class="btn btn-danger">Tidak Terima</a>
                        <a href="?id=<?= $promote["id"] ?>&product=<?= $product["id"] ?>&action=accept" class="btn btn-success">Terima</a>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
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
<h5 class="mt-5">Jasa yang dipromosikan</h5>
<!-- tabel jasa yang terpromosi -->
<div class="table-responsive">
    <table class="table" style="width: 100%;">
        <thead>
            <tr>
                <th scope="col">Nama Jasa</th>
                <th scope="col">Owner Jasa</th>
                <th scope="col">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <!-- loop promosi -->
            <?php foreach ($promotedProducts as $product) :

                $owner = getUserById($product["owner"])["username"];

            ?>
                <tr>
                    <td><?= $product["name"] ?></td>
                    <td><?= $owner ?></td>
                    <td>
                        <a href="?product=<?= $product["id"] ?>&action=delete" class="btn btn-danger">Hapus Dari Jasa Yang Dipromosikan</a>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>
<?php require "template/end.php" ?>