<?php

namespace App\Models;

use CodeIgniter\Model;
use \Hermawan\DataTables\DataTable;

class FilesModel extends Model
{
    protected $table = 'files';
    protected $allowedFields = ['name', 'path'];

    protected $useTimestamps = true;
    
    
}