<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Province;
use App\Models\Municipality;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use ZipArchive;

trait ExportTrait
{
    /**
     * Get provinces for dropdowns
     */
    public function getProvinces()
    {
        $provinces = Province::orderBy('province_name')->get();
        return response()->json($provinces);
    }

    /**
     * Get municipalities by province_id
     */
    public function getMunicipalities(Request $request)
    {
        $provinceId = $request->query('province_id');
        $municipalities = Municipality::where('province_id', $provinceId)
            ->orderBy('municipality_name')
            ->get();
        return response()->json($municipalities);
    }

    /**
     * Get updated data counts
     */
    public function getUpdatedData()
    {
        $data = [
            'total' => $this->model::count(),
            'onShelf' => $this->model::where('status', 'ON-SHELF')->count(),
            'unavailable' => $this->model::where('status', 'UNAVAILABLE')->count(),
            'borrowed' => $this->model::where('status', 'BORROWED')->count(),
        ];

        $records = $this->model::with(['province', 'municipality'])->get();

        return response()->json([
            'counts' => $data,
            'records' => $records,
        ]);
    }

    /**
     * Export to Excel
     */
    public function export(Request $request)
    {
        // Timeout handling for long exports (5 minutes)
        set_time_limit(300);
        ignore_user_abort(true);

        $provinceId = $request->query('province_id');
        $municipalityId = $request->query('municipality_id');

        $query = $this->model::with(['province', 'municipality']);

        if ($provinceId) {
            $query->where('province_id', $provinceId);
        }

        if ($municipalityId) {
            $query->where('municipality_id', $municipalityId);
        }

        $records = $query->get();

        // Get filter details for logging and filename (matching exportFiles)
        $provinceName = $provinceId ? (Province::find($provinceId)?->province_name ?? '') : 'all-provinces';
        $municipalityName = $municipalityId ? (Municipality::find($municipalityId)?->municipality_name ?? '') : '';

        $filterDescription = $provinceId ? 'Province: ' . $provinceName : 'All Provinces';
        if ($municipalityName) {
            $filterDescription .= ', Municipality: ' . $municipalityName;
        }

        // Location part for filename (matching exportFiles)
        $locationPart = '';
        if ($provinceName) {
            $locationPart = strtolower(str_replace(' ', '_', $provinceName));
            if ($municipalityName) {
                $locationPart .= '_' . strtolower(str_replace(' ', '_', $municipalityName));
            }
        }

        // Log activity
        $this->logActivity(
            'EXPORT-' . date('YmdHis'),
            $this->recordType . ' Export - ' . $records->count() . ' records',
            $filterDescription,
            'Exported ' . strtolower($this->recordType) . ' records'
        );

        // Create Excel
        $config = $this->getExportConfig();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Headers
        $column = 'A';
        foreach ($config['headers'] as $header) {
            $sheet->setCellValue($column . '1', $header);
            $column++;
        }

        // Data
        $row = 2;
        foreach ($records as $record) {
            foreach ($config['columns'] as $colIndex => $field) {
                $value = $record->{$field} ?? '';
                if (str_starts_with($field, 'province.') || str_starts_with($field, 'municipality.')) {
                    [$rel, $prop] = explode('.', $field, 2);
                    $value = $record->{$rel}->{$prop} ?? '';
                }
                $sheet->setCellValue(chr(65 + $colIndex) . $row, $value);
            }
            $row++;
        }

        // Auto-size
        foreach (range('A', chr(65 + count($config['headers']) - 1)) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = strtolower($this->recordType) . '_records_' . $locationPart . '_' . date('Y-m-d_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
        exit;
    }

    /**
     * Export to SQL INSERT
     */
    public function exportSql(Request $request)
    {
        // Timeout handling for long exports (5 minutes)
        set_time_limit(300);
        ignore_user_abort(true);

        $provinceId = $request->query('province_id');
        $municipalityId = $request->query('municipality_id');

        $query = $this->model::query();

        if ($provinceId) {
            $query->where('province_id', $provinceId);
        }

        if ($municipalityId) {
            $query->where('municipality_id', $municipalityId);
        }

        $records = $query->get();

        // Filter logging and filename (matching export)
        $provinceName = $provinceId ? (Province::find($provinceId)?->province_name ?? '') : 'all-provinces';
        $municipalityName = $municipalityId ? (Municipality::find($municipalityId)?->municipality_name ?? '') : '';

        $filterDescription = 'All Records';
        if ($provinceName !== 'all-provinces') {
            $filterDescription = 'Province: ' . $provinceName;
            if ($municipalityName) {
                $filterDescription .= ', Municipality: ' . $municipalityName;
            }
        }

        // Location part for filename (matching exportFiles)
        $locationPart = '';
        if ($provinceName) {
            $locationPart = strtolower(str_replace(' ', '_', $provinceName));
            if ($municipalityName) {
                $locationPart .= '_' . strtolower(str_replace(' ', '_', $municipalityName));
            }
        }

        $this->logActivity(
            'EXPORT-SQL-' . date('YmdHis'),
            $this->recordType . ' SQL Export - ' . $records->count() . ' records',
            $filterDescription,
            'Exported ' . strtolower($this->recordType) . ' records to SQL'
        );

        $config = $this->getExportConfig('sql');
        $columns = $config['columns'];
        $values = [];

        foreach ($records as $record) {
            $valueRow = [];
            foreach ($columns as $field) {
                $value = $record->{$field} ?? null;
                if ($value === null) {
                    $valueRow[] = 'NULL';
                } elseif (str_starts_with($field, 'province_id') || str_starts_with($field, 'municipality_id') || str_contains($field, '_id')) {
                    $valueRow[] = (int) $value;
                } else {
                    $filesJson = str_contains($field, 'files') ? json_encode(json_decode($value, true), JSON_UNESCAPED_UNICODE) : $value;
                    $valueRow[] = $this->escapeSql($filesJson ?? '');
                }
            }
            // Add timestamps
            $valueRow[] = $this->escapeSql($record->created_at ?? now()->toDateTimeString());
            $valueRow[] = $this->escapeSql($record->updated_at ?? now()->toDateTimeString());
            $values[] = '(' . implode(', ', $valueRow) . ')';
        }

        $tableName = $this->getTableName();
        $sql = "-- " . strtolower($this->recordType) . "_records_" . $locationPart . "_" . date('Y-m-d_H-i-s') . ".sql\n";
        $sql .= "INSERT INTO {$tableName} (" . implode(', ', $columns) . ", created_at, updated_at) VALUES\n";
        $sql .= implode(",\n", $values);
        $sql .= ";\n";

        $filename = strtolower($this->recordType) . '_records_' . $locationPart . '_' . date('Y-m-d_H-i-s') . '.sql';
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Content-Length: ' . strlen($sql));

        echo $sql;
        
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
        exit;
    }

    /**
     * Export files as ZIP
     */
    public function exportFiles(Request $request)
    {
        // Timeout handling for long exports (5 minutes)
        set_time_limit(300);
        ignore_user_abort(true);

        $provinceId = $request->query('province_id');
        $municipalityId = $request->query('municipality_id');

        $query = $this->model::query();

        if ($provinceId) {
            $query->where('province_id', $provinceId);
        }

        if ($municipalityId) {
            $query->where('municipality_id', $municipalityId);
        }

        $records = $query->get();

        $provinceName = $provinceId ? (Province::find($provinceId)?->province_name ?? '') : 'all-provinces';
        $municipalityName = $municipalityId ? (Municipality::find($municipalityId)?->municipality_name ?? '') : '';

        $filterDescription = 'All Provinces';
        if ($provinceId) {
            $filterDescription = 'Province: ' . $provinceName;
        }
        if ($municipalityName) {
            $filterDescription .= ', Municipality: ' . $municipalityName;
        }

        // Collect files
        $filesByDocket = [];
        foreach ($records as $record) {
            $files = json_decode($record->files, true) ?? [];
            $activeFiles = array_filter($files, fn($file) => !($file['archived'] ?? false));
            $recordFiles = [];
            foreach ($activeFiles as $file) {
                if (Storage::disk('local')->exists($file['path'])) {
                    $fileName = ($file['name'] ?? pathinfo($file['original_name'], PATHINFO_FILENAME)) . '.pdf';
                    $recordFiles[] = ['path' => $file['path'], 'name' => $fileName];
                }
            }
            if ($recordFiles) {
                $filesByDocket[$record->docket_no] = $recordFiles;
            }
        }

        if (empty($filesByDocket)) {
            return response()->json(['success' => false, 'message' => 'No files found in the selected records.'])->header('Content-Type', 'application/json');
        }

        $locationPart = '';
        if ($provinceName) {
            $locationPart = strtolower(str_replace(' ', '_', $provinceName));
            if ($municipalityName) {
                $locationPart .= '_' . strtolower(str_replace(' ', '_', $municipalityName));
            }
        }
        $zipFileName = strtolower($this->recordType) . '_files_' . $locationPart . '_' . date('Y-m-d_His') . '.zip';
        $tempPath = storage_path('app/temp/' . $zipFileName);
        
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $zip = new ZipArchive();
        if ($zip->open($tempPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($filesByDocket as $docketNo => $recordFiles) {
                foreach ($recordFiles as $file) {
                    $fileContent = Storage::disk('local')->get($file['path']);
                    $zip->addFromString($docketNo . '/' . $file['name'], $fileContent);
                }
            }
            $zip->close();
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to create ZIP.']);
        }

        $totalFiles = array_sum(array_map('count', $filesByDocket));
        $this->logActivity(
            'EXPORT-FILES-' . date('YmdHis'),
            $this->recordType . ' Files Export - ' . $totalFiles . ' files from ' . count($filesByDocket) . ' records',
            $filterDescription,
            'Exported ' . strtolower($this->recordType) . ' files'
        );

        $response = response()->download($tempPath, $zipFileName)->deleteFileAfterSend(true);
        
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
        return $response;
    }

    /**
     * Escape SQL value
     */
    private function escapeSql($value)
    {
        if ($value === null) return 'NULL';
        return "'" . addslashes((string) $value) . "'";
    }

    /**
     * Get export configuration - OVERRIDE IN CONTROLLERS
     */
    abstract protected function getExportConfig(string $type = 'excel'): array;

    /**
     * Get table name - OVERRIDE if needed
     */
    protected function getTableName(): string
    {
        return (new ($this->model))->getTable();
    }
}
