<?php

session_start();

require "functions.php";

// Mengecek jika user login atau tidak
if (!isset($_SESSION["userId"])) {
    header("Location: /login.php");
    exit();
}

if (isset($_POST["name"]) && isset($_POST["gender"]) && isset($_POST["address"]) && isset($_POST["phone"]) && isset($_POST["email"]) && isset($_FILES["image"])) {
    // Edit Profile
    $result = editProfile($_SESSION["userId"], $_POST);
    if ($result["success"]) {
        $success = "Profil berhasil diubah";
    } else {
        $error = $result["error"];
    }
}

if (isset($_POST["password"]) && isset($_POST["password2"])) {
    // Change Password
    $result = changePassword($_SESSION["userId"], $_POST);
    if ($result["success"]) {
        $success2 = "Password berhasil diubah";
    } else {
        $error2 = $result["error"];
    }
}

if (isset($_POST["name"]) && isset($_POST["category"]) && isset($_POST["price"]) && isset($_POST["description"]) && isset($_FILES["image"])) {
    // Create Product
    $result = createProduct($_SESSION["userId"], $_POST);
    if ($result["success"]) {
        $success3 = "Jasa berhasil dibuat";
        header("Location: jasa.php?id=" . $result["id"]);
    } else {
        $error3 = $result["error"];
    }
}

// Mengecek apakah user ada di database
$user = getUserById($_SESSION["userId"]);
if (!$user) {
    http_response_code(500);
    echo "Something went wrong";
    exit();
}

// list jasa
$products = getProudctsByOwner($user["id"]);

// Template
$page = "Profile";
require "template/start.php";

?>
<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <?= isLoggedInAsAdmin($_SESSION) ? '<a href="admin" class="btn btn-secondary">Admin Page</a>' : "" ?>
                <!-- Form edit profil -->
                <form method="POST" enctype="multipart/form-data">
                    <!-- Pesan error -->
                    <?= (isset($error)) ? '<div class="alert alert-danger" role="alert">' . $error . '</div>' : "" ?>
                    <?= (isset($success)) ? '<div class="alert alert-success" role="alert">' . $success . '</div>' : "" ?>
                    <img src="<?= $user["image"] ?>" class="rounded-circle d-flex m-auto" height="150">
                    <label for="image" id="image-label" class="btn btn-success btn-sm mt-2 mb-2 w-100">Ubah Gambar</label>
                    <input hidden type="file" accept=".jpg,.jpeg,.png" name="image" id="image">
                    <h5 class="text-center mt-2"><?= $user["username"] ?></h5>
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= $user["name"] ?>">
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Jenis Kelamin</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="gender" id="gender1" value="0" <?= !$user["gender"] ? "checked" : "" ?>>
                            <label class="form-check-label" for="gender1">
                                Laki-laki
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="gender" id="gender2" value="1" <?= $user["gender"] ? "checked" : "" ?>>
                            <label class="form-check-label" for="gender2">
                                Perempuan
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Alamat</label>
                        <input type="text" class="form-control" id="address" name="address" value="<?= $user["address"] ?>">
                    </div>
                    <div class="mb-3">
                        <label for="province" class="form-label">Kota</label>
                        <select id="province" class="form-select" disabled>
                            <option value="" disabled selected>Pilih Provinsi</option>
                        </select>
                        <select name="city" id="city" class="form-select mt-2" disabled>
                            <option value="" disabled selected>Pilih Kota</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Nomor HP</label>
                        <div class="input-group">
                            <span class="input-group-text">+62</span>
                            <input type="number" class="form-control" id="phone" name="phone" min="8100000000" max="819999999999" value="<?= $user["phone"] ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required value="<?= $user["email"] ?>">
                    </div>
                    <button type="submit" class="btn btn-success">Ubah Profil</button>
                </form>
                <hr>
                <!-- Form ganti password -->
                <h5>Ganti Password</h5>
                <!-- Pesan error ganti password -->
                <?= (isset($error2)) ? '<div class="alert alert-danger" role="alert">' . $error2 . '</div>' : "" ?>
                <?= (isset($success2)) ? '<div class="alert alert-success" role="alert">' . $success2 . '</div>' : "" ?>
                <form method="post">
                    <div class="mb-3">
                        <label for="password" class="form-label">Password Lama</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="password2" class="form-label">Password Baru</label>
                        <input type="password" class="form-control" id="password2" name="password2" required>
                    </div>
                    <button type="submit" class="btn btn-success">Ubah Password</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg mt-3 mt-lg-0">
        <!-- List jasa user -->
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <h4>Jasa Saya</h4>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-success" id="open-modal" data-bs-toggle="modal" data-bs-target="#modal"><i class="fa-solid fa-plus"></i> Tambah Jasa</button>
                    </div>
                </div>
                <?= count($products) <= 0 ? '<p class="fst-italic fw-light">Anda tidak memiliki jasa</p>' : "" ?>
                <div class="row">
                    <!-- Loop jasa user -->
                    <?php foreach ($products as $product) : ?>
                        <a href="jasa.php?id=<?= $product["id"] ?>" class="text-decoration-none text-dark col-md-6 col-lg-4 mt-3">
                            <div class="card bg-light">
                                <img src="<?= $product["image"] ?>" class="card-img-top">
                                <div class="card-body">
                                    <h5 class="card-title"><?= $product["name"] ?></h5>
                                    <p><?= substr($product["description"], 0, 50) ?>...</p>
                                    <div class="row">
                                        <div class="col">
                                            <b><?= $user["city"] ?></b>
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
    </div>
</div>

<!-- Form Untuk Tambah Jasa -->
<div class="modal fade" id="modal" tabindex="-1" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <?php if (empty($user["phone"]) || empty($user["city"])) : ?>
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Jasa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        Anda harus mengisi nomor hp dan kota sebelum membuat jasa
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Baik</button>
                </div>
            <?php else : ?>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Jasa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <!-- Pesan error tambah jasa -->
                            <?= (isset($error3)) ? '<div class="alert alert-danger" role="alert">' . $error3 . '</div>' : "" ?>
                            <?= (isset($success3)) ? '<div class="alert alert-success" role="alert">' . $success3 . '</div>' : "" ?>
                            <div class="mb-3">
                                <label for="product-name" class="form-label">Nama</label>
                                <input type="text" class="form-control" id="product-name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="category" class="form-label">Kategori</label>
                                <select name="category" id="category" class="form-select" required>
                                    <option value="" disabled selected>Pilih Kategori</option>
                                    <?php foreach (getCategories() as $id => $category) echo "<option value=\"$id\">$category</option>" ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="price" class="form-label">Harga</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control" id="price" name="price" min="500" max="100000000" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Deskripsi</label>
                                <textarea class="form-control" name="description" id="description" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="product-image" class="form-label">Gambar</label>
                                <input class="form-control" type="file" accept=".jpg,.jpeg,.png" name="image" id="product-image" required>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" name="online" id="online">
                                <label class="form-check-label" for="online">Jasa dapat dilaksanakan secara daring</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Tambah</button>
                    </div>
                </form>
            <?php endif ?>
        </div>
    </div>
</div>

<script>
    // Image Label
    const imageInput = document.querySelector("#image");
    const imageLabel = document.querySelector("#image-label");

    imageInput.addEventListener("change", (e) => {
        imageLabel.textContent = "File Terpilih | " + imageInput.files[0].name;
    })

    // City Selector
    const province = document.querySelector("#province");
    const city = document.querySelector("#city");

    fetch("https://dev.farizdotid.com/api/daerahindonesia/provinsi").then(res => res.json().then((data) => {
        province.disabled = false;
        let options = '<option value="" disabled selected>Pilih Provinsi</option>';
        data.provinsi.forEach(p => {
            options += `<option value="${p.id}">${p.nama}</option>`;
        });
        province.innerHTML = options;
        <?php if ($user["city_id"]) : ?>
            fetch("https://dev.farizdotid.com/api/daerahindonesia/kota/<?= $user["city_id"] ?>").then(res => res.json().then((data) => {
                province.value = data.id_provinsi;
                fetch(`https://dev.farizdotid.com/api/daerahindonesia/kota?id_provinsi=${province.value}`).then(res => res.json().then((data) => {
                    city.disabled = false;
                    let options = '<option value="" disabled selected>Pilih Kota</option>';
                    data.kota_kabupaten.forEach(p => {
                        options += `<option value="${p.id}">${p.nama}</option>`;
                    });
                    city.innerHTML = options;
                    city.value = <?= $user["city_id"] ?>;
                }));
            }));
        <?php endif ?>
    }));

    province.addEventListener("change", () => {
        if (!province.value) {
            city.innerHTML = '<option value="" disabled selected>Pilih Kota</option>';
            city.disabled = true;
            return;
        }
        fetch(`https://dev.farizdotid.com/api/daerahindonesia/kota?id_provinsi=${province.value}`).then(res => res.json().then((data) => {
            city.disabled = false;
            let options = '<option value="" disabled selected>Pilih Kota</option>';
            data.kota_kabupaten.forEach(p => {
                options += `<option value="${p.id}">${p.nama}</option>`;
            });
            city.innerHTML = options;
        }));
    });

    <?php if (isset($error3) || isset($success3)) : ?>
        // Open Add Product Modal
        setTimeout(() => {
            document.querySelector("#open-modal").click();
        });
    <?php endif ?>
</script>
<?php require "template/end.php" ?>