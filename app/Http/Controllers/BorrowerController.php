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
            'borrower_name' => 'required|string|max:255',
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
}
