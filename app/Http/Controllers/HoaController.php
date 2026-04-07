<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HoaDatabase;

class HoaController extends Controller
{
    use FileControllerTrait, ActivityLoggingTrait, ExportTrait;

    public function __construct()
    {
        $this->model = HoaDatabase::class;
        $this->folder = 'hoa_files';
        $this->recordType = 'HOA';
    }

    /**
     * Export configuration for HOA records
     */
    protected function getExportConfig(string $type = 'excel'): array
    {
        if ($type === 'sql') {
            return [
                'columns' => [
                    'hoa_id',
                    'docket_no',
                    'hoa_name',
                    'classification',
                    'hoa_status',
                    'location',
                    'province_id',
                    'municipality_id',
                    'status',
                    'quantity',
                    'remarks',
                    'files',
                ],
            ];
        }

        return [
            'headers' => [
                'HOA ID',
                'Docket No',
                'HOA Name',
                'Classification',
                'HOA Status',
                'Location',
                'Province',
                'Municipality',
                'Status',
                'Quantity',
                'Remarks',
            ],
            'columns' => [
                'hoa_id',
                'docket_no',
                'hoa_name',
                'classification',
                'hoa_status',
                'location',
                'province.province_name',
                'municipality.municipality_name',
                'status',
                'quantity',
                'remarks',
            ],
        ];
    }

    /**
     * Store a new HOA record
     */
    public function store(Request $request)
    {
        $rules = [
            'hoa_id' => 'required|integer|unique:hoa_database,hoa_id',
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
        ];

        $customMessages = [
            'hoa_id.unique' => 'HOA ID already exists. Please use a unique HOA ID.',
        ];

        try {
            $request->validate($rules, $customMessages);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->errors()['hoa_id'][0] ?? 'Validation failed. Please check your input.',
            ], 422);
        }

        try {
            $hoa = HoaDatabase::create($request->all());

            // Log activity
            $this->logActivity($request->docket_no, null, 'HOA Records', 'Added a docket');

            return response()->json([
                'success' => true,
                'message' => 'HOA record added successfully.',
                'hoa' => $hoa,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            if (str_contains($e->getMessage(), 'Duplicate entry') || str_contains($e->getMessage(), 'Integrity constraint violation')) {
                return response()->json([
                    'success' => false,
                    'message' => 'HOA ID already exists. Please use a unique HOA ID.',
                ], 422);
            }
            throw $e;
        }
    }

    /**
     * Update an existing HOA record
     */
    public function update(Request $request, $docketNo)
    {
        $hoa = HoaDatabase::where('docket_no', $docketNo)->firstOrFail();

        $rules = [
            'hoa_id' => 'required|integer|unique:hoa_database,hoa_id,' . $hoa->id,
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
        ];

        $customMessages = [
            'hoa_id.unique' => 'HOA ID already exists. Please use a unique HOA ID.',
        ];

        try {
            $request->validate($rules, $customMessages);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->errors()['hoa_id'][0] ?? 'Validation failed. Please check your input.',
            ], 422);
        }

        try {
            $oldDocketNo = $hoa->docket_no;
            $hoa->update($request->all());

            // Log activity
            $this->logActivity($request->docket_no, $oldDocketNo, 'HOA Records', 'Updated a docket');

            return response()->json([
                'success' => true,
                'message' => 'HOA record updated successfully.',
                'hoa' => $hoa->load(['province', 'municipality']),
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            if (str_contains($e->getMessage(), 'Duplicate entry') || str_contains($e->getMessage(), 'Integrity constraint violation')) {
                return response()->json([
                    'success' => false,
                    'message' => 'HOA ID already exists. Please use a unique HOA ID.',
                ], 422);
            }
            throw $e;
        }
    }
}
