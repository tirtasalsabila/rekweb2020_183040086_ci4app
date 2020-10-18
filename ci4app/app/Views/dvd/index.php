<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>

<div class="container">
    <div class="row">
        <div class="col">
            <h1 class="mt-2">Daftar DVD</h1>
            <?php if (session()->getFlashdata('pesan')) : ?>
                <div class="alert alert-success" role="alert">
                    <?= session()->getFlashdata('pesan'); ?>
                </div>
            <?php endif; ?>
            <table class="table table-striped table-dark">
                <thead>
                    <tr class="text-light">
                        <th scope="col">No</th>
                        <th scope="col">Sampul</th>
                        <th scope="col">Judul</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; ?>
                    <?php foreach ($dvd as $d) : ?>
                        <tr>
                            <th scope="row"><?= $i++; ?></th>
                            <td><img src="/img/<?= $d['sampul']; ?>" alt="" class="sampul"></td>
                            <td><?= $d['judul']; ?></td>
                            <td>
                                <a href="/dvd/<?= $d['slug']; ?>" class="btn btn-outline-primary">Detail</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <a href="/dvd/create" class="btn btn-outline-success mb-3">Tambah Data Dvd</a>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>