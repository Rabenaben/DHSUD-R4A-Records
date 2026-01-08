<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RemDatabase;
use Illuminate\Support\Facades\Log;


class RemController extends Controller
{
    use FileControllerTrait;

    public function __construct()
    {
        $this->model = RemDatabase::class;
        $this->folder = 'rem_files';
        $this->recordType = 'REM';
    }

    public function store(Request $request)
    {
        $request->validate([
            'docket_no' => 'required|string|unique:rem,docket_no',
            'project_name' => 'required|string',
            'province' => 'required|string',
            'status' => 'required|in:ON-SHELF,BORROWED,UNAVAILABLE',
            'quantity' => 'nullable|numeric',
            'remarks' => 'nullable|string',
        ]);

        Log::info('Creating REM record with data: ' . json_encode($request->all()));

        try {
            $rem = RemDatabase::create($request->all());

            Log::info('REM record created successfully with id: ' . $rem->id . ', docket_no: ' . $rem->docket_no);

            return response()->json([
                'success' => true,
                'message' => 'REM record added successfully.',
                'rem' => $rem,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create REM record: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to add REM record.',
            ], 500);
        }
    }
}
