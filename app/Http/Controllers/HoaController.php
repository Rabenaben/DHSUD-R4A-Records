<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HoaDatabase;
use App\Models\Municipality;

class HoaController extends Controller
{
    use FileControllerTrait;

    public function __construct()
    {
        $this->model = HoaDatabase::class;
        $this->folder = 'hoa_files';
        $this->recordType = 'HOA';
    }

    public function store(Request $request)
    {
        $request->validate([
            'docket_no' => 'required|string|unique:hoa_database,docket_no',
            'hoa_name' => 'required|string',
            'location' => 'required|string',
            'province_id' => 'required|exists:provinces,province_id',
            'municipality_id' => 'required|exists:municipalities,municipality_id',
            'status' => 'required|in:ON-SHELF,BORROWED,UNAVAILABLE',
            'quantity' => 'nullable|numeric',
            'remarks' => 'nullable|string',
        ]);

        $hoa = HoaDatabase::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'HOA record added successfully.',
            'hoa' => $hoa,
        ]);
    }

    public function getMunicipalities(Request $request)
    {
        $provinceId = $request->query('province_id');
        $municipalities = Municipality::where('province_id', $provinceId)->orderBy('municipality_name')->get();

        return response()->json($municipalities);
    }

    public function getUpdatedData()
    {
        $data = [
            'total' => HoaDatabase::count(),
            'onShelf' => HoaDatabase::where('status', 'ON-SHELF')->count(),
            'unavailable' => HoaDatabase::where('status', 'UNAVAILABLE')->count(),
            'borrowed' => HoaDatabase::where('status', 'BORROWED')->count(),
        ];

        $records = HoaDatabase::with(['province', 'municipality'])->get();

        return response()->json([
            'counts' => $data,
            'records' => $records,
        ]);
    }
}
