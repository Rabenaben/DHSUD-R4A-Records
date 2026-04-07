<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\RemDatabase;
use App\Models\Province;
use App\Models\Municipality;

class RemController extends Controller
{
    use FileControllerTrait, ActivityLoggingTrait, ExportTrait;

    public function __construct()
    {
        $this->model = RemDatabase::class;
        $this->folder = 'rem_files';
        $this->recordType = 'REM';
    }

    /**
     * REQUIRED by ExportTrait
     */
    protected function getExportConfig(string $type = 'excel'): array
    {
        if ($type === 'sql') {
            return [
                'columns' => [
                    'docket_no',
                    'project_name',
                    'location',
                    'province_id',
                    'municipality_id',
                    'status',
                    'quantity',
                    'remarks',
                    'files'
                ]
            ];
        }

        return [
            'headers' => [
                'Docket No',
                'Project Name',
                'Location',
                'Province',
                'Municipality',
                'Status',
                'Quantity',
                'Remarks'
            ],
            'columns' => [
                'docket_no',
                'project_name',
                'location',
                'province.province_name',
                'municipality.municipality_name',
                'status',
                'quantity',
                'remarks'
            ]
        ];
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

        // KEEP your original logging style
        $province = Province::find($request->province_id);
        $provinceName = $province ? $province->province_name : 'Unknown';

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

        // KEEP your original logging style
        $province = Province::find($request->province_id);
        $provinceName = $province ? $province->province_name : 'Unknown';

        $this->logActivity($request->docket_no, $oldDocketNo, 'REM - ' . $provinceName, 'Updated a docket');

        return response()->json([
            'success' => true,
            'message' => 'REM record updated successfully.',
            'rem' => $rem,
        ]);
    }

    /**
     * KEEP THIS (you had this, so we keep it)
     */
    public function folder($provinceId)
    {
        $records = RemDatabase::with(['province', 'municipality'])
            ->where('province_id', $provinceId)
            ->get();

        return view('rem_records.partials.folder-table', ['records' => $records]);
    }
}