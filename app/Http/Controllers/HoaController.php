<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HoaDatabase;
use App\Models\Municipality;
use App\Models\Province;

class HoaController extends Controller
{
    use FileControllerTrait, ActivityLoggingTrait;

    public function __construct()
    {
        $this->model = HoaDatabase::class;
        $this->folder = 'hoa_files';
        $this->recordType = 'HOA';
    }

    public function getProvinces()
    {
        $provinces = Province::orderBy('province_name')->get();
        return response()->json($provinces);
    }

    public function getMunicipalities(Request $request)
    {
        $provinceId = $request->query('province_id');
        $municipalities = Municipality::where('province_id', $provinceId)->orderBy('municipality_name')->get();

        return response()->json($municipalities);
    }

    public function store(Request $request)
    {
        $request->validate([
            'hoa_id' => 'required|integer',
            'docket_no' => 'required|string',
            'hoa_name' => 'required|string',
            'classification' => 'required|string',
            'hoa_status' => 'required|string|in:REGISTERED,NOT REGISTERED,DENIED,SUSPENDED,REVOKED/CANCELLED,DISSOLVED',
            'location' => 'required|string',
            'province_id' => 'required|exists:provinces,province_id',
            'municipality_id' => 'required|exists:municipalities,municipality_id',
            'status' => 'required|in:ON-SHELF,UNAVAILABLE',
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

    public function update(Request $request, $docketNo)
    {
        $hoa = HoaDatabase::where('docket_no', $docketNo)->firstOrFail();

        $request->validate([
            'hoa_id' => 'required|integer',
            'docket_no' => 'required|string',
            'hoa_name' => 'required|string',
            'classification' => 'required|string',
            'hoa_status' => 'required|string|in:REGISTERED,NOT REGISTERED,DENIED,SUSPENDED,REVOKED/CANCELLED,DISSOLVED',
            'location' => 'required|string',
            'province_id' => 'required|exists:provinces,province_id',
            'municipality_id' => 'required|exists:municipalities,municipality_id',
            'status' => 'required|in:ON-SHELF,UNAVAILABLE',
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

    /**
     * Export HOA records to Excel
     */
    public function export(Request $request)
    {
        $provinceId = $request->query('province_id');
        $municipalityId = $request->query('municipality_id');

        $query = HoaDatabase::with(['province', 'municipality']);

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
            'HOA Export - ' . $records->count() . ' records',
            $filterDescription,
            'Exported HOA records'
        );

        // Create Excel file
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $headers = ['Docket No', 'HOA Name', 'Classification', 'HOA Status', 'Location', 'Province', 'Municipality', 'Status', 'Quantity', 'Remarks'];
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $column++;
        }

        // Set data
        $row = 2;
        foreach ($records as $record) {
            $sheet->setCellValue('A' . $row, $record->docket_no);
            $sheet->setCellValue('B' . $row, $record->hoa_name);
            $sheet->setCellValue('C' . $row, $record->classification);
            $sheet->setCellValue('D' . $row, $record->hoa_status);
            $sheet->setCellValue('E' . $row, $record->location);
            $sheet->setCellValue('F' . $row, $record->province->province_name ?? '');
            $sheet->setCellValue('G' . $row, $record->municipality->municipality_name ?? '');
            $sheet->setCellValue('H' . $row, $record->status);
            $sheet->setCellValue('I' . $row, $record->quantity);
            $sheet->setCellValue('J' . $row, $record->remarks);
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'J') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Download file
        $filename = 'hoa_records_' . date('Y-m-d_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
}
