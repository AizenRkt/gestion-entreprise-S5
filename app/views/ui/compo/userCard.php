<div class="card">
    <div class="card-body py-1 px-3">
        <div class="d-flex align-items-center">
            <div class="avatar avatar-xl">
                <img src="<?= Flight::base() ?>/public/uploads/photos/default.png" alt="Face 1">
            </div>
            <div class="ms-3 name">
                <h5 class="fw-semibold mb-0"><?= $_SESSION['user']['username'] ?></h5>
                <h6 class="text-muted mb-0"><?= $_SESSION['user']['role'] ?></h6>
            </div>
        </div>
    </div>
</div>