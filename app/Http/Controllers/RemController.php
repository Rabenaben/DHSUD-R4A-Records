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

    /**
     * Export REM records to Excel
     */
    public function export(Request $request)
    {
        $provinceId = $request->query('province_id');
        $municipalityId = $request->query('municipality_id');

        $query = RemDatabase::with(['province', 'municipality']);

        if ($provinceId) {
            $query->where('province_id', $provinceId);
        }

        if ($municipalityId) {
            $query->where('municipality_id', $municipalityId);
        }

        $records = $query->get();

        // Get filter details for logging
        $provinceName = '';
        if ($provinceId) {
            $province = Province::find($provinceId);
            $provinceName = $province ? $province->province_name : '';
        }
        $municipalityName = '';
        if ($municipalityId) {
            $municipality = Municipality::find($municipalityId);
            $municipalityName = $municipality ? $municipality->municipality_name : '';
        }

        // Build filter description for logging
        $filterDescription = 'All Provinces';
        if ($provinceName) {
            $filterDescription = 'Province: ' . $provinceName;
            if ($municipalityName) {
                $filterDescription .= ', Municipality: ' . $municipalityName;
            }
        }

        // Log the export activity
        $this->logActivity(
            'EXPORT-' . date('YmdHis'),
            'REM Export - ' . $records->count() . ' records',
            $filterDescription,
            'Exported REM records'
        );

        // Create Excel file
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $headers = ['Docket No', 'Project Name', 'Location', 'Province', 'Municipality', 'Status', 'Quantity', 'Remarks'];
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $column++;
        }

        // Set data
        $row = 2;
        foreach ($records as $record) {
            $sheet->setCellValue('A' . $row, $record->docket_no);
            $sheet->setCellValue('B' . $row, $record->project_name);
            $sheet->setCellValue('C' . $row, $record->location);
            $sheet->setCellValue('D' . $row, $record->province->province_name ?? '');
            $sheet->setCellValue('E' . $row, $record->municipality->municipality_name ?? '');
            $sheet->setCellValue('F' . $row, $record->status);
            $sheet->setCellValue('G' . $row, $record->quantity);
            $sheet->setCellValue('H' . $row, $record->remarks);
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'H') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Download file
        $filename = 'rem_records_' . date('Y-m-d_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
}
