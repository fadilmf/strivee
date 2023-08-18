<?php

session_start();

require "../functions.php";

if (!isLoggedInAsAdmin($_SESSION)) {
    header("Location: /login.php");
    exit();
}

$pagination = pagination(isset($_GET["page"]) && is_numeric($_GET["page"]) ? $_GET["page"] : 1, query("SELECT COUNT(*) FROM `users`")[0]["COUNT(*)"]);
$users = getUsers($pagination["start"]);

$page = "Pengguna";
require "template/start.php";

?>
<h1>Pengguna</h1>
<!-- tabel user -->
<div class="table-responsive">
    <table class="table" style="width: 100%;">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Username</th>
                <th scope="col">Email</th>
                <th scope="col">Nama Lengkap</th>
                <th scope="col">Jenis Kelamin</th>
                <th scope="col">Alamat</th>
                <th scope="col">Nomor HP</th>
                <th scope="col">Kota</th>
                <th scope="col">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <!-- loop user -->
            <?php foreach ($users as $user) : ?>
                <tr>
                    <th scope="row"><?= $user["id"] ?></th>
                    <td><?= $user["username"] ?></td>
                    <td><?= $user["email"] ?></td>
                    <td><?= $user["name"] ?></td>
                    <td><?= $user["gender"] ? "Perempuan" : "Laki-laki" ?></td>
                    <td><?= $user["address"] ?></td>
                    <td>+62 <?= $user["phone"] ?></td>
                    <td><?= $user["city"] ?></td>
                    <td><a href="deleteUser.php?id=<?= $user["id"] ?>" class="btn btn-danger">Hapus User</a></td>
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