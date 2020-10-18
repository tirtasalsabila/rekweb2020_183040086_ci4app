<?php

namespace App\Models;

use CodeIgniter\Model;

class DvdModel extends Model
{
    protected $table = 'dvd';
    protected $useTimestamps = true;
    protected $allowedFields = ['judul', 'slug', 'genre', 'penerbit', 'sampul'];

    public function getDvd($slug = false)
    {
        if ($slug == false) {
            return $this->findAll();
        }

        return $this->where(['slug' => $slug])->first();
    }
}
