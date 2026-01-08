<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RemDatabase;

class RemController extends Controller
{
    use FileControllerTrait, ActivityLoggingTrait;

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

        $rem = RemDatabase::create($request->all());

        // Log activity
        $this->logActivity($request->docket_no, null, 'REM - ' . $request->province, 'Added a docket');

        return response()->json([
            'success' => true,
            'message' => 'REM record added successfully.',
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
