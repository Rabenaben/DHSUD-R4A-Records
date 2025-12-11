<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Borrower;

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
            'borrower_name' => 'required|string|max:255|unique:borrowers,borrower_name',
            'docket_number' => 'required|string|max:100',
            'file_location' => 'required|string|max:255',
            'date_borrowed' => 'required|date',
        ]);

        $borrower = Borrower::create(array_merge($validated, ['status' => 'Borrowed']));

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
            'status' => 'required|string|in:Borrowed,Returned',
        ]);

        $borrower->update($validated);

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

        // Ensure status is correct based on date_returned
        foreach ($borrowers as $borrower) {
            if ($borrower->date_returned && $borrower->status !== 'Returned') {
                $borrower->status = 'Returned';
                $borrower->save();
            } elseif (!$borrower->date_returned && $borrower->status !== 'Borrowed') {
                $borrower->status = 'Borrowed';
                $borrower->save();
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

        // Automatically set status based on date_returned
        $status = $borrower->fresh()->date_returned ? 'Returned' : 'Borrowed';
        $borrower->update(['status' => $status]);

        return response()->json([
            'success' => true,
            'borrower' => $borrower,
            'message' => 'Returned date updated successfully.'
        ]);
    }
}
