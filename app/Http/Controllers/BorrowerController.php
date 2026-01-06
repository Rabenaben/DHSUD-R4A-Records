<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Borrower;
use App\Models\RemDatabase;
use App\Models\HoaDatabase;

class BorrowerController extends Controller
{
    // 🔹 Show Borrower Details
    public function showBorrower($id)
    {
        $borrower = Borrower::findOrFail($id);

        return response()->json([
            'success' => true,
            'borrower' => $borrower
        ]);
    }

    // 🔹 Store Borrower
    public function storeBorrower(Request $request)
    {
        $validated = $request->validate([
            'borrower_name' => 'required|string|max:255',
            'docket_number' => 'required|string|max:100',
            'file_location' => 'required|string|max:255',
            'date_borrowed' => 'required|date',
        ]);

        // Check if docket is already borrowed
        if ($validated['file_location'] === 'REM Records') {
            $docket = RemDatabase::where('docket_no', $validated['docket_number'])->first();
            if ($docket && $docket->status === 'BORROWED') {
                return response()->json([
                    'success' => false,
                    'message' => 'This docket is already borrowed. Choose another.'
                ]);
            }
        } elseif ($validated['file_location'] === 'HOA Records') {
            $docket = HoaDatabase::where('docket_no', $validated['docket_number'])->first();
            if ($docket && $docket->status === 'BORROWED') {
                return response()->json([
                    'success' => false,
                    'message' => 'This docket is already borrowed. Choose another.'
                ]);
            }
        }

        $borrower = Borrower::create($validated);

        // Update docket status to BORROWED
        if ($validated['file_location'] === 'REM Records') {
            RemDatabase::where('docket_no', $validated['docket_number'])->update(['status' => 'BORROWED']);
        } elseif ($validated['file_location'] === 'HOA Records') {
            HoaDatabase::where('docket_no', $validated['docket_number'])->update(['status' => 'BORROWED']);
        }

        return response()->json([
            'success' => true,
            'borrower' => $borrower,
            'message' => 'Borrower record created successfully.'
        ]);
    }

    // 🔹 Update Borrower
    public function updateBorrower(Request $request, $id)
    {
        $borrower = Borrower::findOrFail($id);

        $validated = $request->validate([
            'borrower_name' => 'required|string|max:255',
            'docket_number' => 'required|string|max:100',
            'file_location' => 'required|string|max:255',
            'date_borrowed' => 'required|date',
            'date_returned' => 'nullable|date',
        ]);

        $borrower->update($validated);

        // Update docket status based on date_returned
        $docketStatus = $borrower->fresh()->date_returned ? 'ON-SHELF' : 'BORROWED';
        if ($validated['file_location'] === 'REM Records') {
            RemDatabase::where('docket_no', $validated['docket_number'])->update(['status' => $docketStatus]);
        } elseif ($validated['file_location'] === 'HOA Records') {
            HoaDatabase::where('docket_no', $validated['docket_number'])->update(['status' => $docketStatus]);
        }

        return response()->json([
            'success' => true,
            'borrower' => $borrower,
            'message' => 'Borrower record updated successfully.'
        ]);
    }

    // 🔹 Get Borrower History
    public function getBorrowerHistory($borrowerName)
    {
        $borrowers = Borrower::where('borrower_name', $borrowerName)->orderBy('date_borrowed', 'desc')->get();

        // Add status from docket tables
        foreach ($borrowers as $borrower) {
            if ($borrower->file_location === 'REM Records') {
                $docket = RemDatabase::where('docket_no', $borrower->docket_number)->first();
                $borrower->status = $docket ? ($docket->status === 'BORROWED' ? 'Borrowed' : 'Returned') : 'Unknown';
            } elseif ($borrower->file_location === 'HOA Records') {
                $docket = HoaDatabase::where('docket_no', $borrower->docket_number)->first();
                $borrower->status = $docket ? ($docket->status === 'BORROWED' ? 'Borrowed' : 'Returned') : 'Unknown';
            } else {
                $borrower->status = 'Unknown';
            }
        }

        return response()->json([
            'success' => true,
            'borrower_name' => $borrowerName,
            'history' => $borrowers
        ]);
    }

    // 🔹 Update Returned Date
    public function updateReturnedDate(Request $request, $id)
    {
        $borrower = Borrower::findOrFail($id);

        $validated = $request->validate([
            'date_returned' => 'nullable|date',
        ]);

        // Update the borrower with the new date_returned
        $borrower->update($validated);

        // Update docket status based on date_returned
        $docketStatus = $borrower->fresh()->date_returned ? 'ON-SHELF' : 'BORROWED';
        if ($borrower->file_location === 'REM Records') {
            RemDatabase::where('docket_no', $borrower->docket_number)->update(['status' => $docketStatus]);
        } elseif ($borrower->file_location === 'HOA Records') {
            HoaDatabase::where('docket_no', $borrower->docket_number)->update(['status' => $docketStatus]);
        }

        return response()->json([
            'success' => true,
            'borrower' => $borrower,
            'message' => 'Returned date updated successfully.'
        ]);
    }
}
