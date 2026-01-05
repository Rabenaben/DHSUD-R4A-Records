<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RemDatabase;

class RemController extends Controller
{
    use FileControllerTrait;

    public function __construct()
    {
        $this->model = RemDatabase::class;
        $this->folder = 'rem_files';
        $this->recordType = 'REM';
    }
}
