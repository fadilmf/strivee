<?php

session_start();

require "../functions.php";

if (!isLoggedInAsAdmin($_SESSION)) {
    header("Location: /login.php");
    exit();
}

$pagination = pagination(isset($_GET["page"]) && is_numeric($_GET["page"]) ? $_GET["page"] : 1, query("SELECT COUNT(*) FROM `reports`")[0]["COUNT(*)"]);
$reports = query("SELECT * FROM `reports` LIMIT " . $pagination["start"] . ", 10");

$page = "Report";
require "template/start.php";

?>
<h1>Report</h1>
<!-- tabel report -->
<div class="table-responsive">
    <table class="table" style="width: 100%;">
        <thead>
            <tr>
                <th scope="col">Jasa</th>
                <th scope="col">Pelapor</th>
                <th scope="col">Alasan</th>
                <th scope="col">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <!-- loop report -->
            <?php foreach ($reports as $report) :

                $product = getProductById($report["product"])["name"];
                $reporter = getUserById($report["reporter"])["username"];

            ?>
                <tr>
                    <td><?= $product ?></td>
                    <td><?= $reporter ?></td>
                    <td><?= $report["reason"] ?></td>
                    <td><a href="/delete.php?id=<?= $report["product"] ?>&admin=true" class="btn btn-danger">Hapus Jasa</a></td>
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
<?php require "template/end.php" ?>