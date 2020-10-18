<?php

namespace App\Controllers;

use App\Models\DvdModel;

class Dvd extends BaseController
{
    protected $dvdModel;

    public function __construct()
    {
        $this->dvdModel = new DvdModel();
    }

    public function index()
    {
        // $dvd = $this->dvdModel->findAll();

        $data = [
            'title' => 'Daftar Dvd',
            'dvd' => $this->dvdModel->getDvd()
        ];

        // $dvdModel = new \App\Models\DvdModel();
        // $dvdModel = new DvdModel();

        return view('dvd/index', $data);
    }

    public function detail($slug)
    {
        $data = [
            'title' => 'Detail Dvd',
            'dvd' => $this->dvdModel->getDvd($slug)
        ];

        // jika dvd tidak ada di tabel
        if (empty($data['dvd'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Judul dvd ' . $slug . ' tidak ditemukan.');
        }

        return view('dvd/detail', $data);
    }

    public function create()
    {
        // session();
        $data = [
            'title' => 'Form Tambah Data Dvd',
            'validation' => \Config\Services::validation()
        ];

        return view('dvd/create', $data);
    }

    public function save()
    {
        //validasi input
        if (!$this->validate([
            'judul' => [
                'rules' => 'required|is_unique[dvd.judul]',
                'errors' => [
                    'required' => '{field} dvd harus diisi',
                    'is_unique' => '{field} dvd sudah terdaftar'
                ]
            ],
            'sampul' => [
                'rules' => 'is_image[sampul]|mime_in[sampul,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'is_image' => 'Must upload jpg/jpeg/png file',
                    'mime_in' => 'Must upload jpg/jpeg/png file'
                ]
            ]
        ])) {
            // $validation = \Config\Services::validation();
            // return redirect()->to('/dvd/create')->withInput()->with('validation', $validation);
            return redirect()->to('/dvd/create')->withInput();
        }

        // ambil gambar
        $fileSampul = $this->request->getFile('sampul');
        // if not image
        if ($fileSampul->getError() == 4) {
            $namaSampul = 'default.jpeg';
        } else {
            // generate nama sampul random
            $namaSampul = $fileSampul->getRandomName();
            // pindahkan file ke folder img
            $fileSampul->move('img', $namaSampul);
        }

        $slug = url_title($this->request->getVar('judul'), '-', true);
        $this->dvdModel->save([
            'judul' => $this->request->getVar('judul'),
            'slug' => $slug,
            'genre' => $this->request->getVar('genre'),
            'penerbit' => $this->request->getVar('penerbit'),
            'sampul' => $namaSampul
        ]);

        session()->setFlashdata('pesan', 'Daftar DVD berhasil ditambahkan.');

        return redirect()->to('/dvd');
    }

    public function delete($id)
    {
        // find iamge based id
        $dvd = $this->dvdModel->find($id);

        // check if default image
        if ($dvd['sampul'] != 'default.jpeg') {
            // delete image
            unlink('img/' . $dvd['sampul']);
        }

        $this->dvdModel->delete($id);
        session()->setFlashdata('pesan', 'Daftar DVD berhasil dihapus.');
        return redirect()->to('/dvd');
    }

    public function edit($slug)
    {
        $data = [
            'title' => 'Form Edit Data Dvd',
            'validation' => \Config\Services::validation(),
            'dvd' => $this->dvdModel->getDvd($slug)
        ];

        return view('dvd/edit', $data);
    }

    public function update($id)
    {
        // cek judul
        $dvdLama = $this->dvdModel->getDvd($this->request->getVar('slug'));
        if ($dvdLama['judul'] == $this->request->getVar('judul')) {
            $rule_judul = 'required';
        } else {
            $rule_judul = 'required|is_unique[dvd.judul]';
        }

        if (!$this->validate([
            'judul' => [
                'rules' => $rule_judul,
                'errors' => [
                    'required' => '{field} dvd harus diisi',
                    'is_unique' => '{field} dvd sudah terdaftar'
                ]
            ],
            'sampul' => [
                'rules' => 'is_image[sampul]|mime_in[sampul,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'is_image' => 'Must upload jpg/jpeg/png file',
                    'mime_in' => 'Must upload jpg/jpeg/png file'
                ]
            ]
        ])) {
            // $validation = \Config\Services::validation();
            return redirect()->to('/dvd/edit/' . $this->request->getVar('slug'))->withInput();
        }

        $fileSampul = $this->request->getFile('sampul');

        //check image if last image
        if ($fileSampul->getError() == 4) {
            $namaSampul = $this->request->getVar('sampuLama');
        } else {
            //generate last file name 
            $namaSampul = $fileSampul->getRandomName();
            // move image
            $fileSampul->move('img', $namaSampul);
            //delete last file
            unlink('img/' . $this->request->getVar('sampulLama'));
        }

        $slug = url_title($this->request->getVar('judul'), '-', true);
        $this->dvdModel->save([
            'id' => $id,
            'judul' => $this->request->getVar('judul'),
            'slug' => $slug,
            'genre' => $this->request->getVar('genre'),
            'penerbit' => $this->request->getVar('penerbit'),
            'sampul' => $namaSampul
        ]);

        session()->setFlashdata('pesan', 'Data DVD berhasil diubah.');

        return redirect()->to('/dvd');
    }
}
