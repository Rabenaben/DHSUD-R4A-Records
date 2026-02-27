<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RemDatabase;
use App\Models\Province;
use App\Models\Municipality;

class RemController extends Controller
{
    use FileControllerTrait, ActivityLoggingTrait;

    public function __construct()
    {
        $this->model = RemDatabase::class;
        $this->folder = 'rem_files';
        $this->recordType = 'REM';
    }

    /**
     * Get all provinces for REM dropdowns
     */
    public function getProvinces()
    {
        $provinces = Province::orderBy('province_name')->get();
        return response()->json($provinces);
    }

    /**
     * Get municipalities filtered by province_id for REM dropdowns
     */
    public function getMunicipalities(Request $request)
    {
        $provinceId = $request->query('province_id');
        
        if ($provinceId) {
            $municipalities = Municipality::where('province_id', $provinceId)
                ->orderBy('municipality_name')
                ->get();
        } else {
            $municipalities = Municipality::orderBy('municipality_name')->get();
        }
        
        return response()->json($municipalities);
    }

    public function store(Request $request)
    {
        $request->validate([
            'docket_no' => 'required|string',
            'project_name' => 'required|string',
            'location' => 'required|string',
            'province_id' => 'required|integer',
            'municipality_id' => 'required|integer',
            'status' => 'required|in:ON-SHELF,UNAVAILABLE',
            'quantity' => 'nullable|numeric',
            'remarks' => 'nullable|string',
        ]);

        $rem = RemDatabase::create($request->all());

        // Get province name for logging
        $province = Province::find($request->province_id);
        $provinceName = $province ? $province->province_name : 'Unknown';

        // Log activity
        $this->logActivity($request->docket_no, null, 'REM - ' . $provinceName, 'Added a docket');

        return response()->json([
            'success' => true,
            'message' => 'REM record added successfully.',
            'rem' => $rem,
        ]);
    }

    public function update(Request $request, $docketNo)
    {
        $rem = RemDatabase::where('docket_no', $docketNo)->firstOrFail();

        $request->validate([
            'docket_no' => 'required|string',
            'project_name' => 'required|string',
            'location' => 'required|string',
            'province_id' => 'required|integer',
            'municipality_id' => 'required|integer',
            'status' => 'required|in:ON-SHELF,UNAVAILABLE',
            'quantity' => 'nullable|numeric',
            'remarks' => 'nullable|string',
        ]);

        $oldDocketNo = $rem->docket_no;
        $rem->update($request->all());

        // Get province name for logging
        $province = Province::find($request->province_id);
        $provinceName = $province ? $province->province_name : 'Unknown';

        // Log activity
        $this->logActivity($request->docket_no, $oldDocketNo, 'REM - ' . $provinceName, 'Updated a docket');

        return response()->json([
            'success' => true,
            'message' => 'REM record updated successfully.',
            'rem' => $rem,
        ]);
    }

    public function getUpdatedData()
    {
        $data = [
            'total' => RemDatabase::count(),
            'onShelf' => RemDatabase::where('status', 'ON-SHELF')->count(),
            'unavailable' => RemDatabase::where('status', 'UNAVAILABLE')->count(),
            'borrowed' => RemDatabase::where('status', 'BORROWED')->count(),
        ];

        $records = RemDatabase::all();

        return response()->json([
            'counts' => $data,
            'records' => $records,
        ]);
    }
}
