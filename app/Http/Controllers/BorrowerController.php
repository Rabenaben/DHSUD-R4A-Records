<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Borrower;
use App\Models\RemDatabase;
use App\Models\HoaDatabase;

class BorrowerController extends Controller
{
    use ActivityLoggingTrait;

    // 🔹 Show Borrower Details
    public function showBorrower($id)
    {
        $borrower = Borrower::findOrFail($id);

        return response()->json([
            'success' => true,
            'borrower' => $borrower
        ]);
    }

    // 🔹 Validate Docket Exists
    private function validateDocketExists($fileLocation, $docketNumber)
    {
        if ($fileLocation === 'REM Records') {
            $docket = RemDatabase::where('docket_no', $docketNumber)->first();
            if (!$docket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Docket number "' . $docketNumber . '" No existing records'
                ]);
            }
        } elseif ($fileLocation === 'HOA Records') {
            $docket = HoaDatabase::where('docket_no', $docketNumber)->first();
            if (!$docket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error "' . $docketNumber . '" No existing records.'
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Please select a valid file location (HOA Records or REM Records).'
            ]);
        }
        return null; // Valid
    }

    // 🔹 Update Docket Status
    private function updateDocketStatus($fileLocation, $docketNumber, $status)
    {
        if ($fileLocation === 'REM Records') {
            RemDatabase::where('docket_no', $docketNumber)->update(['status' => $status]);
        } elseif ($fileLocation === 'HOA Records') {
            HoaDatabase::where('docket_no', $docketNumber)->update(['status' => $status]);
        }
    }

    // 🔹 Store Borrower
    public function storeBorrower(Request $request)
    {
        $validated = $request->validate([
            'borrower_name' => 'required|string|max:255',
            'docket_number' => 'required|string|max:100',
            'file_location' => 'required|string|max:255',
            'date_borrowed' => 'nullable|date',
        ]);

        // Trim docket number to avoid whitespace issues
        $validated['docket_number'] = trim($validated['docket_number']);

        // Validate docket exists
        $validationError = $this->validateDocketExists($validated['file_location'], $validated['docket_number']);
        if ($validationError) return $validationError;

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

        // Set date_borrowed to current datetime if not provided
        if (!isset($validated['date_borrowed']) || empty($validated['date_borrowed'])) {
            $validated['date_borrowed'] = now();
        } else {
            // If date is provided, append current time
            $validated['date_borrowed'] = $validated['date_borrowed'] . ' ' . now()->format('H:i:s');
        }

        $borrower = Borrower::create($validated);

        // Set status for the new borrower
        $borrower->status = 'Borrowed';

        // Update docket status to BORROWED
        $this->updateDocketStatus($validated['file_location'], $validated['docket_number'], 'BORROWED');

        // Log the borrow activity
        $this->logActivity($validated['docket_number'], null, $validated['file_location'], 'Borrow');

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

        // Update date_borrowed to current datetime
        $validated['date_borrowed'] = now();

        $borrower->update($validated);

        // Update docket status based on date_returned
        $docketStatus = $borrower->fresh()->date_returned ? 'ON-SHELF' : 'BORROWED';
        if ($validated['file_location'] === 'REM Records') {
            RemDatabase::where('docket_no', $validated['docket_number'])->update(['status' => $docketStatus]);
        } elseif ($validated['file_location'] === 'HOA Records') {
            HoaDatabase::where('docket_no', $validated['docket_number'])->update(['status' => $docketStatus]);
        }

        // Log activity
        $this->logActivity($validated['docket_number'], null, $validated['file_location'], 'Borrow');

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
            $borrower->status = is_null($borrower->date_returned) ? 'Borrowed' : 'Returned';
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
        $this->updateDocketStatus($borrower->file_location, $borrower->docket_number, $docketStatus);

        // Calculate the borrower's overall status
        $borrowerRecords = Borrower::where('borrower_name', $borrower->borrower_name)->get();
        $hasBorrowed = $borrowerRecords->contains(function ($record) {
            return is_null($record->date_returned);
        });
        $borrowerStatus = $hasBorrowed ? 'Borrowed' : 'Returned';

        // Log activity if returned
        if ($borrower->fresh()->date_returned) {
            $this->logActivity(
                $borrower->docket_number,
                null,
                $borrower->file_location,
                'Return'
            );
        }

        return response()->json([
            'success' => true,
            'borrower' => $borrower,
            'borrower_status' => $borrowerStatus,
            'message' => 'Returned date updated successfully.'
        ]);
    }
}
