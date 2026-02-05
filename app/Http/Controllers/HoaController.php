<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HoaDatabase;
use App\Models\Municipality;

class HoaController extends Controller
{
    use FileControllerTrait, ActivityLoggingTrait;

    public function __construct()
    {
        $this->model = HoaDatabase::class;
        $this->folder = 'hoa_files';
        $this->recordType = 'HOA';
    }

    public function store(Request $request)
    {
        $request->validate([
            'hoa_id' => 'required|integer',
            'docket_no' => 'required|string|unique:hoa_database,docket_no',
            'hoa_name' => 'required|string',
            'classification' => 'required|string',
            'hoa_status' => 'required|string|in:REGISTERED,NOT REGISTERED,DENIED,SUSPENDED,REVOKED/CANCELLED,DISSOLVED',
            'location' => 'required|string',
            'province_id' => 'required|exists:provinces,province_id',
            'municipality_id' => 'required|exists:municipalities,municipality_id',
            'status' => 'required|in:ON-SHELF,BORROWED,UNAVAILABLE',
            'quantity' => 'nullable|numeric',
            'remarks' => 'nullable|string',
        ]);

        $hoa = HoaDatabase::create($request->all());

        // Log activity
        $this->logActivity($request->docket_no, null, 'HOA Records', 'Added a docket');

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

    public function update(Request $request, $docketNo)
    {
        $hoa = HoaDatabase::where('docket_no', $docketNo)->firstOrFail();

        $request->validate([
            'hoa_id' => 'required|integer',
            'docket_no' => 'required|string|unique:hoa_database,docket_no,' . $hoa->id,
            'hoa_name' => 'required|string',
            'classification' => 'required|string',
            'hoa_status' => 'required|string|in:REGISTERED,NOT REGISTERED,DENIED,SUSPENDED,REVOKED/CANCELLED,DISSOLVED',
            'location' => 'required|string',
            'province_id' => 'required|exists:provinces,province_id',
            'municipality_id' => 'required|exists:municipalities,municipality_id',
            'status' => 'required|in:ON-SHELF,BORROWED,UNAVAILABLE',
            'quantity' => 'nullable|numeric',
            'remarks' => 'nullable|string',
        ]);

        $oldDocketNo = $hoa->docket_no;
        $hoa->update($request->all());

        // Log activity
        $this->logActivity($request->docket_no, $oldDocketNo, 'HOA Records', 'Updated a docket');

        return response()->json([
            'success' => true,
            'message' => 'HOA record updated successfully.',
            'hoa' => $hoa->load(['province', 'municipality']),
        ]);
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
