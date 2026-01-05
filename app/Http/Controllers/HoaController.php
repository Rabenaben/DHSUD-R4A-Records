<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HoaDatabase;

class HoaController extends Controller
{
    use FileControllerTrait;

    public function __construct()
    {
        $this->model = HoaDatabase::class;
        $this->folder = 'hoa_files';
        $this->recordType = 'HOA';
    }
}
